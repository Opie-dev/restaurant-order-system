<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableQrCode;
use App\Models\Table;
use App\Services\Admin\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class AdminQrController extends Controller
{
    public function __construct(
        private QrCodeService $qrCodeService
    ) {}

    /**
     * Display a listing of QR codes
     */
    public function index(): View
    {
        $qrCodes = TableQrCode::with(['table', 'table.store'])
            ->orderBy('table_id')
            ->paginate(20);

        return view('admin.qr-codes.index', compact('qrCodes'));
    }

    /**
     * Show QR code generation form
     */
    public function create(): View
    {
        $tables = Table::where('is_active', true)
            ->with('store')
            ->orderBy('store_id')
            ->orderBy('table_number')
            ->get();

        return view('admin.qr-codes.create', compact('tables'));
    }

    /**
     * Generate QR code for a table
     */
    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $table = Table::findOrFail($validated['table_id']);

        try {
            $qrCode = $this->qrCodeService->generateForTable($table);

            if (isset($validated['expires_at'])) {
                $qrCode->update(['expires_at' => $validated['expires_at']]);
            }

            return redirect()->route('admin.qr-codes.index')
                ->with('success', "QR code generated successfully for {$table->display_name}.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to generate QR code: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Generate QR codes for all active tables
     */
    public function generateAll(): RedirectResponse
    {
        try {
            $results = $this->qrCodeService->generateForAllTables();

            return redirect()->route('admin.qr-codes.index')
                ->with('success', "Generated QR codes for " . count($results) . " tables successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to generate QR codes: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified QR code
     */
    public function show(TableQrCode $qrCode): View
    {
        $qrCode->load(['table', 'table.store']);

        return view('admin.qr-codes.show', compact('qrCode'));
    }

    /**
     * Download single QR code as PNG
     */
    public function download(TableQrCode $qrCode): Response
    {
        $filePath = $this->qrCodeService->downloadQrCode($qrCode);
        $fileName = "table-{$qrCode->table->table_number}-qr-code.png";

        return response()->download($filePath, $fileName);
    }

    /**
     * Download QR code as PDF with branding
     */
    public function downloadPdf(TableQrCode $qrCode): Response
    {
        $filePath = $this->qrCodeService->downloadQrCodePdf($qrCode);
        $fileName = "table-{$qrCode->table->table_number}-qr-code.pdf";

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Download QR code with table number overlay
     */
    public function downloadWithTableNumber(TableQrCode $qrCode): Response
    {
        $filePath = $this->qrCodeService->downloadQrCodeWithTableNumber($qrCode);
        $fileName = "table-{$qrCode->table->table_number}-qr-code-with-number.png";

        return response()->download($filePath, $fileName);
    }

    /**
     * Download multiple QR codes as ZIP
     */
    public function bulkDownloadZip(Request $request): Response
    {
        $qrCodeIds = $request->input('qr_codes', []);
        $qrCodes = TableQrCode::whereIn('id', $qrCodeIds)->get();

        if ($qrCodes->isEmpty()) {
            return back()->withErrors(['error' => 'No QR codes selected for download.']);
        }

        $filePath = $this->qrCodeService->downloadBulkQrCodes($qrCodes->toArray());
        $fileName = 'qr-codes-' . now()->format('Y-m-d-H-i-s') . '.zip';

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Download all active QR codes as ZIP
     */
    public function bulkDownloadAll(): Response
    {
        $qrCodes = TableQrCode::where('is_active', true)->get();

        if ($qrCodes->isEmpty()) {
            return back()->withErrors(['error' => 'No active QR codes found.']);
        }

        $filePath = $this->qrCodeService->downloadBulkQrCodes($qrCodes->toArray());
        $fileName = 'all-qr-codes-' . now()->format('Y-m-d-H-i-s') . '.zip';

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Regenerate QR code
     */
    public function regenerate(TableQrCode $qrCode): RedirectResponse
    {
        try {
            $table = $qrCode->table;

            // Deactivate current QR code
            $qrCode->update(['is_active' => false]);

            // Generate new QR code
            $newQrCode = $this->qrCodeService->generateForTable($table);

            return redirect()->route('admin.qr-codes.index')
                ->with('success', "QR code regenerated successfully for {$table->display_name}.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to regenerate QR code: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle QR code active status
     */
    public function toggleStatus(TableQrCode $qrCode): RedirectResponse
    {
        $qrCode->update(['is_active' => !$qrCode->is_active]);

        $status = $qrCode->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "QR code {$status} successfully.");
    }

    /**
     * Remove the specified QR code
     */
    public function destroy(TableQrCode $qrCode): RedirectResponse
    {
        $tableName = $qrCode->table->display_name;

        // Delete QR code image files
        $imageFiles = [
            "qr-codes/table-{$qrCode->table_id}-{$qrCode->qr_code}.png",
            "qr-codes/table-{$qrCode->table_id}-{$qrCode->qr_code}-logo.png",
            "qr-codes/table-{$qrCode->table_id}-{$qrCode->qr_code}-with-number.png",
        ];

        foreach ($imageFiles as $file) {
            if (Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }

        $qrCode->delete();

        return redirect()->route('admin.qr-codes.index')
            ->with('success', "QR code for {$tableName} deleted successfully.");
    }
}

<?php

namespace App\Services\Admin;

use App\Models\Table;
use App\Models\TableQrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class QrCodeService
{
    /**
     * Generate QR code for a table
     */
    public function generateForTable(Table $table): TableQrCode
    {
        // Deactivate existing QR codes for this table
        $table->qrCodes()->update(['is_active' => false]);

        // Create new QR code
        $qrCode = $table->qrCodes()->create([
            'qr_code' => $this->generateUniqueQrCode($table),
            'qr_url' => $this->generateQrUrl($table),
            'is_active' => true,
            'generated_at' => now(),
        ]);

        // Generate QR code image
        $qrCode->generateQrCodeImage();

        return $qrCode;
    }

    /**
     * Generate QR codes for all active tables
     */
    public function generateForAllTables(): array
    {
        $results = [];

        Table::where('is_active', true)->chunk(50, function ($tables) use (&$results) {
            foreach ($tables as $table) {
                $results[] = $this->generateForTable($table);
            }
        });

        return $results;
    }

    /**
     * Download single QR code as PNG
     */
    public function downloadQrCode(TableQrCode $qrCode): string
    {
        $fileName = "qr/table-{$qrCode->table_id}-{$qrCode->qr_code}.png";

        if (!Storage::disk('public')->exists($fileName)) {
            $qrCode->generateQrCodeImage();
        }

        return Storage::disk('public')->path($fileName);
    }

    /**
     * Download QR code as PDF with branding
     */
    public function downloadQrCodePdf(TableQrCode $qrCode): string
    {
        $qrCodePath = $this->downloadQrCode($qrCode);

        $pdf = Pdf::loadView('admin.qr-codes.pdf-template', [
            'qrCode' => $qrCode,
            'qrCodePath' => $qrCodePath,
            'store' => $qrCode->table->store,
            'table' => $qrCode->table,
        ]);

        $pdfFileName = "qr-code-table-{$qrCode->table->table_number}.pdf";
        $pdfPath = storage_path("app/temp/{$pdfFileName}");

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $pdf->save($pdfPath);

        return $pdfPath;
    }

    /**
     * Download QR code with table number overlay
     */
    public function downloadQrCodeWithTableNumber(TableQrCode $qrCode): string
    {
        $fileName = "qr/table-{$qrCode->table_id}-{$qrCode->qr_code}-with-number.png";

        if (!Storage::disk('public')->exists($fileName)) {
            $qrCode->generateQrCodeWithTableNumber();
        }

        return Storage::disk('public')->path($fileName);
    }

    /**
     * Download multiple QR codes as ZIP
     */
    public function downloadBulkQrCodes(array $qrCodes): string
    {
        $zipFileName = 'qr-codes-' . now()->format('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path("app/temp/{$zipFileName}");

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);

        foreach ($qrCodes as $qrCode) {
            $qrCodePath = $this->downloadQrCode($qrCode);
            $fileName = "table-{$qrCode->table->table_number}-{$qrCode->qr_code}.png";
            $zip->addFile($qrCodePath, $fileName);
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Generate unique QR code identifier
     */
    private function generateUniqueQrCode(Table $table): string
    {
        do {
            $qrCode = 'TBL_' . $table->id . '_' . uniqid();
        } while (TableQrCode::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }

    /**
     * Generate QR code URL
     */
    private function generateQrUrl(Table $table): string
    {
        return route('store.menu.index', ['qrCode' => $this->generateUniqueQrCode($table)]);
    }
}

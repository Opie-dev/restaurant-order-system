<?php

namespace App\Livewire\Admin\QrCodes;

use App\Models\Table;
use App\Models\TableQrCode;
use App\Services\Admin\QrCodeService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.admin')]
class QrGenerator extends Component
{
    #[Validate('required|exists:tables,id')]
    public $table_id = '';

    #[Validate('nullable|date|after:now')]
    public $expires_at = '';

    public function mount(): void
    {
        $prefillTableId = request()->query('table_id');
        if ($prefillTableId) {
            $this->table_id = (string) $prefillTableId;
        }
    }

    public function generate()
    {
        $this->validate();

        $table = Table::findOrFail($this->table_id);

        try {
            $qrCodeService = app(QrCodeService::class);
            $qrCode = $qrCodeService->generateForTable($table);

            if ($this->expires_at) {
                $qrCode->update(['expires_at' => $this->expires_at]);
            }

            $this->dispatch('flash', type: 'success', message: "QR code generated successfully for {$table->display_name}.");

            return redirect()->route('admin.qr-codes.index');
        } catch (\Exception $e) {
            $this->dispatch('flash', type: 'error', message: 'Failed to generate QR code: ' . $e->getMessage());
        }
    }

    public function generateAll()
    {
        try {
            $qrCodeService = app(QrCodeService::class);
            $results = $qrCodeService->generateForAllTables();

            $this->dispatch('flash', type: 'success', message: "Generated QR codes for " . count($results) . " tables successfully.");

            return redirect()->route('admin.qr-codes.index');
        } catch (\Exception $e) {
            $this->dispatch('flash', type: 'error', message: 'Failed to generate QR codes: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $tables = Table::where('is_active', true)
            ->with('store')
            ->orderBy('store_id')
            ->orderBy('table_number')
            ->get();

        return view('livewire.admin.qr-codes.qr-generator', [
            'tables' => $tables,
        ]);
    }
}

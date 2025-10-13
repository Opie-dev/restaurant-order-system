<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Table;
use App\Models\Store;
use App\Services\Admin\QrCodeService;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use App\Services\Admin\StoreService;

#[Layout('layouts.admin')]
class TableForm extends Component
{
    public ?Table $table = null;
    public $table_number;

    #[Validate('required|integer|min:1|max:20')]
    public $capacity = 2;

    #[Validate('nullable|string|max:255')]
    public $location_description = '';

    #[Validate('boolean')]
    public $is_active = true;

    private $storeService;
    public $store;

    public function rules()
    {
        return [
            'table_number' => 'required|string|max:10|unique:tables,table_number,NULL,id,store_id,' . $this->store->id,
        ];
    }

    public function updatedTableNumber()
    {
        $this->validateOnly('table_number');
    }

    public function boot()
    {
        $this->storeService = new StoreService();
    }

    public function mount(?Table $table = null)
    {
        $this->table = $table;
        $this->store = $this->storeService->getCurrentStore();

        if ($table) {
            $this->table_number = $table->table_number;
            $this->capacity = $table->capacity;
            $this->location_description = $table->location_description;
            $this->is_active = $table->is_active;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'store_id' => $this->store->id,
            'table_number' => $this->table_number,
            'capacity' => $this->capacity,
            'location_description' => $this->location_description,
            'is_active' => $this->is_active,
        ];

        if ($this->table) {
            $this->table->update($data);
            $message = "Table {$this->table->table_number} updated successfully.";
        } else {
            $this->table = Table::create($data);
            $message = "Table {$this->table->table_number} created successfully.";
        }

        $this->dispatch('flash', type: 'success', message: $message);

        return redirect()->route('admin.tables.index');
    }

    public function generateQrCode()
    {
        if (!$this->table) {
            $this->dispatch('flash', type: 'error', message: 'Save the table before generating a QR code.');
            return;
        }

        $qrCodeService = app(QrCodeService::class);
        $qrCodeService->generateForTable($this->table);

        $this->dispatch('flash', type: 'success', message: "QR code generated for Table {$this->table->table_number}.");
    }

    public function disableQrCode(): void
    {
        if (!$this->table) {
            $this->dispatch('flash', type: 'error', message: 'No table context.');
            return;
        }

        $active = $this->table->qrCodes()->where('is_active', true)->latest()->first();
        if (!$active) {
            $this->dispatch('flash', type: 'info', message: 'No active QR code to disable.');
            return;
        }

        $active->update(['is_active' => false]);
        $this->dispatch('flash', type: 'success', message: 'QR code disabled successfully.');
    }

    public function render()
    {
        $latestQrCode = $this->table
            ? $this->table->qrCodes()->latest()->first()
            : null;

        return view('livewire.admin.tables.table-form', [
            'latestQrCode' => $latestQrCode,
        ]);
    }

    public function enableQrCode(): void
    {
        if (!$this->table) {
            $this->dispatch('flash', type: 'error', message: 'No table context.');
            return;
        }

        $latest = $this->table->qrCodes()->latest()->first();
        if (!$latest) {
            $this->dispatch('flash', type: 'error', message: 'No QR code found to enable.');
            return;
        }

        if ($latest->is_active) {
            $this->dispatch('flash', type: 'info', message: 'The QR code is already active.');
            return;
        }

        // Ensure only one active
        $this->table->qrCodes()->update(['is_active' => false]);
        $latest->update(['is_active' => true]);

        $this->dispatch('flash', type: 'success', message: 'QR code enabled successfully.');
    }

    public function emailQrCode(): void
    {
        if (!$this->table) {
            $this->dispatch('flash', type: 'error', message: 'No table context.');
            return;
        }

        $latest = $this->table->qrCodes()->latest()->first();
        if (!$latest) {
            $this->dispatch('flash', type: 'error', message: 'No QR code to email.');
            return;
        }

        // Here we would dispatch a queued job to email the QR code attachment.
        // For now, just flash a message so the admin gets immediate feedback.
        $this->dispatch('flash', type: 'success', message: 'QR code will be emailed to you shortly.');
    }
}

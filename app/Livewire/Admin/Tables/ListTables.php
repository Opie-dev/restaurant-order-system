<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Table;
use App\Models\Store;
use App\Models\TableQrCode;
use App\Services\Admin\QrCodeService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Services\Admin\StoreService;

#[Layout('layouts.admin')]
class ListTables extends Component
{
    use WithPagination;

    public $search = '';
    public $storeFilter = '';
    public $statusFilter = '';
    public $perPage = 20;

    // QR Code properties
    public $selectedTableId = '';
    public $expiresAt = '';
    public $showQrForm = false;
    public $store;
    private $storeService;

    protected $queryString = [
        'search' => ['except' => ''],
        'storeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function boot()
    {
        $this->storeService = new StoreService();
    }

    public function mount()
    {
        $this->store = $this->storeService->getCurrentStore();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStoreFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleStatus(Table $table)
    {
        $table->update(['is_active' => !$table->is_active]);

        $status = $table->is_active ? 'activated' : 'deactivated';

        $this->dispatch('flash', type: 'success', message: "Table {$table->table_number} {$status} successfully.");
    }

    public function deleteTable(Table $table)
    {
        if ($table->hasActiveOrder()) {
            $this->dispatch('flash', type: 'error', message: 'Cannot delete table with active orders.');
            return;
        }

        $tableNumber = $table->table_number;
        $table->delete();

        $this->dispatch('flash', type: 'success', message: "Table {$tableNumber} deleted successfully.");
    }

    public function showQrForm($tableId)
    {
        $this->selectedTableId = $tableId;
        $this->showQrForm = true;
        $this->expiresAt = '';
    }

    public function hideQrForm()
    {
        $this->showQrForm = false;
        $this->selectedTableId = '';
        $this->expiresAt = '';
    }

    public function generateQrCode()
    {
        $this->validate([
            'selectedTableId' => 'required|exists:tables,id',
            'expiresAt' => 'nullable|date|after:now',
        ]);

        $table = Table::findOrFail($this->selectedTableId);

        $qrCodeService = app(QrCodeService::class);
        $qrCode = $qrCodeService->generateForTable($table);

        $this->hideQrForm();
        $this->dispatch('flash', type: 'success', message: "QR code generated for Table {$table->table_number}.");
    }

    public function regenerateQrCode(TableQrCode $qrCode)
    {
        $qrCodeService = app(QrCodeService::class);
        $newQrCode = $qrCodeService->generateForTable($qrCode->table);

        $this->dispatch('flash', type: 'success', message: "QR code regenerated for Table {$qrCode->table->table_number}.");
    }


    public function deleteQrCode(TableQrCode $qrCode)
    {
        $tableNumber = $qrCode->table->table_number;
        $qrCode->delete();

        $this->dispatch('flash', type: 'success', message: "QR code for Table {$tableNumber} deleted successfully.");
    }

    public function render()
    {
        $tables = Table::with(['store', 'qrCodes' => function ($query) {
            $query->where('is_active', true)->latest();
        }, 'orders'])
            ->where('store_id', $this->store->id)
            ->when($this->search, function ($query) {
                $query->where('table_number', 'like', '%' . $this->search . '%')
                    ->orWhere('location_description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('store', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->orderBy('table_number')
            ->paginate($this->perPage);

        // Provide a simple list of active tables for the current store for the modal select
        $tablesForSelect = Table::where('store_id', $this->store->id)
            ->where('is_active', true)
            ->orderBy('table_number')
            ->get(['id', 'table_number']);

        return view('livewire.admin.tables.list-tables', [
            'tables' => $tables,
            'tablesForSelect' => $tablesForSelect,
        ]);
    }
}

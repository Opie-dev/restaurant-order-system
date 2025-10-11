<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Store;
use App\Services\Admin\StoreService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class StoreHours extends Component
{
    public ?Store $currentStore = null;
    private $storeService;

    public bool $always_open = false;
    /** @var array<int, array{day:string,enabled:bool,open:string|null,close:string|null}> */
    public array $hours = [];

    public function boot(): void
    {
        $this->storeService = app(StoreService::class);
    }

    public function mount(): void
    {
        $this->currentStore = $this->storeService->getCurrentStore();
        if (!$this->currentStore) {
            $this->redirectRoute('admin.stores.select');
            return;
        }
        $settings = $this->currentStore->settings ?? [];
        $this->always_open = (bool)($settings['always_open'] ?? false);
        $this->hours = $this->defaults();
        if (is_array($settings['opening_hours'] ?? null)) {
            foreach ($this->hours as $i => $row) {
                $saved = $settings['opening_hours'][$i] ?? null;
                if (is_array($saved)) {
                    $this->hours[$i]['enabled'] = (bool)($saved['enabled'] ?? false);
                    $this->hours[$i]['open'] = isset($saved['open']) && $saved['open'] !== null ? $saved['open'] : $row['open'];
                    $this->hours[$i]['close'] = isset($saved['close']) && $saved['close'] !== null ? $saved['close'] : $row['close'];
                }
            }
        }
    }

    private function defaults(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $out = [];
        foreach ($days as $d) {
            $out[] = ['day' => ucfirst($d), 'enabled' => false, 'open' => '08:00', 'close' => '23:00'];
        }
        return $out;
    }

    public function save(): void
    {
        foreach ($this->hours as $i => $row) {
            if (($row['enabled'] ?? false) && ((!preg_match('/^\d{2}:\d{2}$/', $row['open'] ?? '') || !preg_match('/^\d{2}:\d{2}$/', $row['close'] ?? '')) || strtotime((string)$row['open']) >= strtotime((string)$row['close']))) {
                $this->addError("hours.$i", 'Invalid time range.');
                return;
            }

            // Normalize disabled days: store null for open/close when closed
            if (!($row['enabled'] ?? false)) {
                $this->hours[$i]['open'] = null;
                $this->hours[$i]['close'] = null;
            }
        }


        $settings = $this->currentStore->settings ?? [];
        $settings['always_open'] = $this->always_open;
        $settings['opening_hours'] = $this->hours;
        $this->currentStore->update(['settings' => $settings]);

        $this->dispatch('flash', type: 'success', message: 'Opening hours updated successfully.');
    }

    public function updatedAlwaysOpen($value): void
    {
        // Don't modify the hours data when toggling always_open
        // Just keep the current day data intact
    }

    public function updated($name, $value): void
    {
        // If a day's enabled is set to true but open/close is null, set to default values
        if (preg_match('/^hours\.(\d+)\.enabled$/', $name, $matches) === 1) {
            $index = (int)$matches[1];
            if ($value && (empty($this->hours[$index]['open']) || empty($this->hours[$index]['close']))) {
                $this->hours[$index]['open'] = '08:00';
                $this->hours[$index]['close'] = '23:00';
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.store-hours');
    }
}

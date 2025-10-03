<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Store;
use App\Services\Admin\StoreService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class StoreMedia extends Component
{
    use WithFileUploads;

    public ?Store $currentStore = null;
    private $storeService;

    #[Validate('nullable|image|max:2048')]
    public $logo;
    public ?string $logo_path = null;

    #[Validate('nullable|image|max:4096')]
    public $cover;
    public ?string $cover_path = null;

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
        $this->logo_path = $this->currentStore->logo_path;
        $this->cover_path = $this->currentStore->cover_path;
    }

    public function saveMedia(): void
    {
        $this->validate();

        if (!$this->currentStore) {
            session()->flash('error', 'No store selected.');
            return;
        }

        $data = [];
        if ($this->logo) {
            if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
                Storage::disk('public')->delete($this->logo_path);
            }
            $logoPath = $this->logo->store('logos', 'public');
            $data['logo_path'] = $logoPath;
            $this->logo_path = $logoPath;
        }
        if ($this->cover) {
            if ($this->cover_path && Storage::disk('public')->exists($this->cover_path)) {
                Storage::disk('public')->delete($this->cover_path);
            }
            $coverPath = $this->cover->store('covers', 'public');
            $data['cover_path'] = $coverPath;
            $this->cover_path = $coverPath;
        }

        if (!empty($data)) {
            $this->currentStore->update($data);
        }

        $this->logo = null;
        $this->cover = null;
        session()->flash('success', 'Store media updated successfully.');
        $this->dispatch('media-saved');
    }

    public function render()
    {
        return view('livewire.admin.settings.store-media');
    }
}

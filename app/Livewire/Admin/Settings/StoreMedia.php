<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Store;
use App\Services\Admin\StoreService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\Admin\OnboardingService;

#[Layout('layouts.admin')]
class StoreMedia extends Component
{
    use WithFileUploads;

    public ?Store $currentStore = null;

    private $storeService;
    private $onboardingService;

    #[Validate('nullable|image|max:2048')]
    public $logo;
    public ?string $logo_path = null;

    #[Validate('nullable|image|max:4096')]
    public $cover;
    public ?string $cover_path = null;

    // Removed desktop cover support

    protected array $messages = [
        'logo.image' => 'The picture failed to upload because it is not a valid image.',
        'logo.max' => 'The picture failed to upload because it exceeds 2MB.',
        'logo.uploaded' => 'The picture failed to upload because of a network or server error.',
        'cover.image' => 'The picture failed to upload because it is not a valid image.',
        'cover.max' => 'The picture failed to upload because it exceeds 4MB.',
        'cover.uploaded' => 'The picture failed to upload because of a network or server error.',
    ];

    protected array $validationAttributes = [
        'logo' => 'picture',
        'cover' => 'picture',
        'cover_desktop' => 'picture',
    ];

    public function boot(): void
    {
        $this->storeService = new StoreService();
        $this->currentStore = $this->storeService->getCurrentStore();
        $this->onboardingService = new OnboardingService($this->currentStore);
    }

    public function mount(): void
    {

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
            $this->dispatch('flash', type: 'error', message: 'No store selected.');
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

        $this->dispatch('flash', type: 'success', message: 'Store media updated successfully.');
    }

    public function updatedLogo(): void
    {
        $this->validateOnly('logo');
        if (!$this->currentStore || !$this->logo) {
            return;
        }
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            Storage::disk('public')->delete($this->logo_path);
        }
        $logoPath = $this->logo->store('logos', 'public');
        $this->currentStore->update(['logo_path' => $logoPath]);
        $this->logo_path = $logoPath;
        $this->logo = null;

        $this->dispatch('flash', type: 'success', message: 'Store logo updated successfully.');
    }

    public function updatedCover(): void
    {
        $this->validateOnly('cover');
        if (!$this->currentStore || !$this->cover) {
            return;
        }
        if ($this->cover_path && Storage::disk('public')->exists($this->cover_path)) {
            Storage::disk('public')->delete($this->cover_path);
        }
        $coverPath = $this->cover->store('covers', 'public');
        $this->currentStore->update(['cover_path' => $coverPath]);
        $this->cover_path = $coverPath;
        $this->cover = null;

        $this->dispatch('flash', type: 'success', message: 'Store cover updated successfully.');
    }



    public function deleteLogo(): void
    {
        if (!$this->currentStore || !$this->logo_path) {
            return;
        }
        if (Storage::disk('public')->exists($this->logo_path)) {
            Storage::disk('public')->delete($this->logo_path);
        }
        $this->currentStore->update(['logo_path' => null]);
        $this->logo_path = null;

        $this->dispatch('flash', type: 'success', message: 'Store logo deleted successfully.');
    }

    public function deleteCover(): void
    {
        if (!$this->currentStore || !$this->cover_path) {
            return;
        }
        if (Storage::disk('public')->exists($this->cover_path)) {
            Storage::disk('public')->delete($this->cover_path);
        }
        $this->currentStore->update(['cover_path' => null]);
        $this->cover_path = null;

        $this->dispatch('flash', type: 'success', message: 'Store cover deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.store-media');
    }
}

<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Services\Admin\OnboardingService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Services\Admin\StoreService;

#[Layout('layouts.admin')]
#[Title('Create Category')]
class CreateCategory extends Component
{
    public string $name = '';
    public ?int $parentLevel1Id = null;
    public ?int $storeId = null;
    private $storeService;

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name,NULL,id,store_id,' . $this->storeId,
            ],
            'parentLevel1Id' => 'nullable|exists:categories,id',
        ];
    }

    protected $messages = [
        'name.required' => 'Category name is required.',
        'name.unique' => 'A category with this name already exists.',
        'parentLevel1Id.exists' => 'Selected parent category does not exist.',
    ];

    public function boot()
    {
        $this->storeService = new StoreService();
    }

    public function mount()
    {
        $this->storeId = $this->storeService->getCurrentStore()->id;
    }

    public function create()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'parent_id' => $this->parentLevel1Id,
            'is_active' => true,
            'store_id' => $this->storeId,
        ]);

        $this->dispatch('flash', type: 'success', message: 'Category created successfully.');

        // Check if store is in onboarding mode
        $currentStore = $this->storeService->getCurrentStore();
        if ($currentStore->is_onboarding) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.categories.index');
    }

    public function getRootCategoriesProperty()
    {
        return Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.categories.create-category');
    }
}

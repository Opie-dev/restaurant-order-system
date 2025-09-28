<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Create Category')]
class CreateCategory extends Component
{
    public string $name = '';
    public ?int $parentLevel1Id = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
        'parentLevel1Id' => 'nullable|exists:categories,id',
    ];

    protected $messages = [
        'name.required' => 'Category name is required.',
        'name.unique' => 'A category with this name already exists.',
        'parentLevel1Id.exists' => 'Selected parent category does not exist.',
    ];

    public function mount()
    {
        // Initialize component
    }

    public function create()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'parent_id' => $this->parentLevel1Id,
            'is_active' => true,
        ]);

        session()->flash('success', 'Category created successfully.');

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

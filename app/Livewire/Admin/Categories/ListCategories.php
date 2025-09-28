<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class ListCategories extends Component
{

    public string $name = '';
    public string $search = '';

    public ?int $parentLevel1Id = null; // Select a root (or null)
    public $categories;
    public $rootCategories;
    public $hierarchicalCategories;

    public function mount()
    {
        $this->loadCategories();
    }

    public function updatedSearch()
    {
        $this->loadCategories();
    }

    private function loadCategories()
    {
        $query = Category::ordered();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $this->categories = $query->get(['id', 'name', 'is_active', 'position', 'parent_id']);
        $this->rootCategories = Category::whereNull('parent_id')->ordered()->get(['id', 'name', 'position']);
        $this->buildHierarchicalStructure();
    }

    private function buildHierarchicalStructure()
    {
        $this->hierarchicalCategories = collect();
        $categoryMap = $this->categories->keyBy('id');

        // Build tree structure - only root categories initially
        foreach ($this->categories as $category) {
            if ($category->parent_id === null) {
                $this->addCategoryToTree($category, $categoryMap, 0);
            }
        }
    }

    private function addCategoryToTree($category, $categoryMap, $level)
    {
        $category->level = $level;
        $category->hasChildren = $this->categories->where('parent_id', $category->id)->count() > 0;
        $this->hierarchicalCategories->push($category);

        // Add children only if parent is expanded (this will be handled by Alpine.js)
        $children = $this->categories->where('parent_id', $category->id);
        foreach ($children as $child) {
            $this->addCategoryToTree($child, $categoryMap, $level + 1);
        }
    }

    public function create(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'parentLevel1Id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $parentId = $this->parentLevel1Id;

        // Compute next position within siblings (same parent_id)
        $nextPosition = (int) (Category::where('parent_id', $parentId)->max('position') ?? 0) + 1;

        $category = Category::create([
            'name' => $this->name,
            'is_active' => true,
            'parent_id' => $parentId,
            'position' => $nextPosition,
        ]);

        $this->name = '';
        $this->parentLevel1Id = null;
        $this->loadCategories();
        session()->flash('success', 'Category created successfully!');
    }

    public function toggle(int $id): void
    {
        $cat = Category::findOrFail($id);
        $cat->is_active = !$cat->is_active;
        $cat->save();

        $this->loadCategories();
        session()->flash('success', 'Category status updated!');
    }

    public function deleteCategory(int $id): void
    {
        $cat = Category::findOrFail($id);

        if ($cat->children()->exists()) {
            session()->flash('error', 'Cannot delete: category has subcategories.');
            return;
        }
        if ($cat->menuItems()->exists()) {
            session()->flash('error', 'Cannot delete: category has menu items.');
            return;
        }

        $cat->delete();
        $this->loadCategories();
        session()->flash('success', 'Category deleted successfully!');
    }

    public function swapOrder(int $sourceId, int $targetId): void
    {
        if ($sourceId === $targetId) {
            return;
        }
        $source = Category::findOrFail($sourceId);
        $target = Category::findOrFail($targetId);

        $tmp = $source->position;
        $source->update(['position' => $target->position]);
        $target->update(['position' => $tmp]);

        $this->loadCategories();
        session()->flash('success', 'Category order updated!');
    }

    public function render()
    {
        return view('livewire.admin.categories.list-categories');
    }
}

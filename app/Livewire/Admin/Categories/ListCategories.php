<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

#[Layout('layouts.admin')]
class ListCategories extends Component
{
    use WireToast;

    public string $name = '';

    public ?int $parentLevel1Id = null; // Select a root (or null)
    public $categories;
    public $rootCategories;

    public function mount()
    {
        $this->categories = Category::ordered()->get(['id', 'name', 'is_active', 'position', 'parent_id']);
        $this->rootCategories = Category::whereNull('parent_id')->ordered()->get(['id', 'name', 'position']);
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

        $this->js('window.location.reload()');
    }

    public function toggle(int $id): void
    {
        $cat = Category::findOrFail($id);
        $cat->is_active = !$cat->is_active;
        $cat->save();

        $this->js('window.location.reload()');
    }

    public function deleteCategory(int $id): void
    {
        $cat = Category::findOrFail($id);

        if ($cat->children()->exists()) {
            $this->toast()->error('Cannot delete: category has subcategories.');
            return;
        }
        if ($cat->menuItems()->exists()) {
            $this->toast()->error('Cannot delete: category has menu items.');
            return;
        }

        $cat->delete();

        $this->js('window.location.reload()');
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

        $this->js('window.location.reload()');
    }

    public function render()
    {
        return view('livewire.admin.categories.list-categories');
    }
}

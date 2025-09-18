<?php

namespace App\Livewire\Admin\Menu;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListItems extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?int $categoryId = null;

    #[Url]
    public ?string $active = 'all';

    #[Url]
    public string $sort = 'position';

    #[Computed]
    public function categories()
    {
        // Always show root categories
        $rootCategories = Category::whereNull('parent_id')
            ->ordered()
            ->get(['id', 'name', 'position', 'parent_id']);

        // If a root category is selected, also show its subcategories
        if ($this->categoryId) {
            $selectedCategory = Category::find($this->categoryId);
            if ($selectedCategory && $selectedCategory->parent_id === null) {
                // If a root category is selected, show its subcategories
                $subcategories = Category::where('parent_id', $this->categoryId)->ordered()->get(['id', 'name', 'position', 'parent_id']);
                return $rootCategories->concat($subcategories);
            }
        }

        return $rootCategories;
    }

    public function updating($name): void
    {
        if (in_array($name, ['search', 'categoryId', 'active', 'sort'], true)) {
            $this->resetPage();
        }
    }

    #[On('category-selected')]
    public function selectCategory($id = null): void
    {
        $this->categoryId = $id ? (int) $id : null;
        $this->resetPage();
    }

    #[Computed]
    public function items(): Collection
    {
        $query = MenuItem::query()
            ->with('category')
            ->when($this->search !== '', function (Builder $q): void {
                $q->where(
                    fn(Builder $qq): Builder => $qq
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                );
            })
            ->when($this->categoryId, function (Builder $q) {
                $category = Category::find($this->categoryId);
                if ($category) {
                    $childIds = $category->children()->pluck('id');
                    return $q->where(function ($query) use ($category, $childIds) {
                        $query->where('category_id', $category->id)
                            ->orWhereIn('category_id', $childIds);
                    });
                }
                return $q;
            })
            ->when($this->active === 'active', fn(Builder $q): Builder => $q->where('is_active', true))
            ->when($this->active === 'inactive', fn(Builder $q): Builder => $q->where('is_active', false));

        $query = match ($this->sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name'),
            'position' => $query->orderBy('position')->orderBy('name'),
            default => $query->latest(),
        };

        return $query->get();
    }

    #[On('item-toggled')]
    public function toggleActive(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $item->is_active = !$item->is_active;
        $item->save();
    }


    public function deleteItem(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        if ($item->image_path && !str_starts_with($item->image_path, 'http')) {
            Storage::disk('public')->delete($item->image_path);
        }
        $item->delete();
        $this->resetPage();
        session()->flash('success', 'Item deleted.');
    }

    public function render()
    {
        return view('livewire.admin.menu.list-items');
    }
}

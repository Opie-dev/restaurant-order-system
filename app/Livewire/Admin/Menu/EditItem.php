<?php

namespace App\Livewire\Admin\Menu;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Validation\Rules\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class EditItem extends Component
{
    use WithFileUploads;

    public MenuItem $menuItem;

    #[Validate('required|string|min:2|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $description = null;

    #[Validate('required|numeric|min:0|max:999999.99')]
    public $price;

    #[Validate('required|integer|exists:categories,id')]
    public ?int $category_id = null;

    public bool $is_active = true;
    #[Validate('required|integer|min:0')]
    public int $stock = 0;

    #[Validate('nullable|in:popular,bestseller')]
    public ?string $tag = null;

    public $image; // temporary upload

    public function mount(MenuItem $menuItem): void
    {
        $this->menuItem = $menuItem;
        $this->fill([
            'name' => $menuItem->name,
            'description' => $menuItem->description,
            'price' => $menuItem->price,
            'category_id' => $menuItem->category_id,
            'is_active' => (bool) $menuItem->is_active,
            'stock' => (int) ($menuItem->stock ?? 0),
            'tag' => $menuItem->tag,
        ]);
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->image) {
            $this->validate([
                'image' => [File::image()->max(2048)],
            ]);
        }

        $this->menuItem->fill($validated);

        if ($this->image) {
            $path = $this->image->store('menu', 'public');
            $this->menuItem->image_path = $path;
        }

        $this->menuItem->save();

        session()->flash('success', 'Menu item saved.');
        $this->redirectRoute('admin.menu.index');
    }

    public function render()
    {
        return view('livewire.admin.menu.edit-item', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'isEdit' => true,
            'menuItem' => $this->menuItem,
        ]);
    }
}

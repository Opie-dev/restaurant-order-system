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
class CreateItem extends Component
{
    use WithFileUploads;

    public string $name = '';
    public ?string $description = null;
    public $price;
    public ?int $category_id = null;
    public bool $is_active = true;
    public int $stock = 0;
    public ?string $tag = null; // popular | bestseller
    public $image;

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'category_id' => 'required|integer|exists:categories,id',
            'is_active' => 'boolean',
            'stock' => 'required|integer|min:0',
            'tag' => 'nullable|in:popular,bestseller',
        ]);

        if ($this->image) {
            $this->validate([
                'image' => [File::image()->max(2048)],
            ]);
        }

        $item = new MenuItem($validated);

        if ($this->image) {
            $path = $this->image->store('menu', 'public');
            $item->image_path = $path;
        }

        $item->save();

        session()->flash('success', 'Menu item created.');
        $this->redirectRoute('admin.menu.index');
    }

    public function render()
    {
        return view('livewire.admin.menu.edit-item', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'isEdit' => false,
            'menuItem' => null,
        ]);
    }
}

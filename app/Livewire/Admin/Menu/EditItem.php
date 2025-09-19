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

    public $price;

    public $base_price;

    #[Validate('required|integer|exists:categories,id')]
    public ?int $category_id = null;

    public bool $is_active = true;
    public bool $enabled = true;

    #[Validate('required|integer|min:0')]
    public int $stock = 0;

    #[Validate('nullable|in:popular,bestseller')]
    public ?string $tag = null;

    #[Validate('required|in:set,ala_carte')]
    public ?string $type = 'ala_carte';

    #[Validate('nullable|array')]
    public array $options = [];

    #[Validate('nullable|array')]
    public array $addons = [];

    public $image; // temporary upload

    public function mount(MenuItem $menuItem): void
    {
        $this->menuItem = $menuItem;
        $this->fill([
            'name' => $menuItem->name,
            'description' => $menuItem->description,
            'price' => $menuItem->price,
            'base_price' => $menuItem->base_price,
            'category_id' => $menuItem->category_id,
            'is_active' => (bool) $menuItem->is_active,
            'enabled' => (bool) ($menuItem->enabled ?? true),
            'stock' => (int) ($menuItem->stock ?? 0),
            'tag' => $menuItem->tag,
            'type' => $menuItem->type ?? 'ala_carte',
            'options' => $menuItem->options ?? [],
            'addons' => $menuItem->addons ?? [],
        ]);
    }

    public function addOptionGroup(): void
    {
        $this->options[] = [
            'name' => '',
            'enabled' => true,
            'rules' => ['required', 'one'],
            'options' => [
                ['name' => '', 'enabled' => true]
            ]
        ];
    }

    public function removeOptionGroup(int $index): void
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function addOptionOption(int $groupIndex): void
    {
        $this->options[$groupIndex]['options'][] = ['name' => '', 'enabled' => true];
    }

    public function removeOptionOption(int $groupIndex, int $optionIndex): void
    {
        unset($this->options[$groupIndex]['options'][$optionIndex]);
        $this->options[$groupIndex]['options'] = array_values($this->options[$groupIndex]['options']);
    }

    public function addAddonGroup(): void
    {
        $this->addons[] = [
            'name' => '',
            'enabled' => true,
            'rules' => ['required', 'one'],
            'options' => [
                ['name' => '', 'price' => 0, 'enabled' => true]
            ]
        ];
    }

    public function removeAddonGroup(int $index): void
    {
        unset($this->addons[$index]);
        $this->addons = array_values($this->addons);
    }

    public function addAddonOption(int $groupIndex): void
    {
        $this->addons[$groupIndex]['options'][] = ['name' => '', 'price' => 0, 'enabled' => true];
    }

    public function removeAddonOption(int $groupIndex, int $optionIndex): void
    {
        unset($this->addons[$groupIndex]['options'][$optionIndex]);
        $this->addons[$groupIndex]['options'] = array_values($this->addons[$groupIndex]['options']);
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'required|integer|exists:categories,id',
            'is_active' => 'boolean',
            'enabled' => 'boolean',
            'stock' => 'required|integer|min:0',
            'tag' => 'nullable|in:popular,bestseller',
            'type' => 'required|in:set,ala_carte',
            'options' => 'nullable|array',
            'options.*.enabled' => 'sometimes|boolean',
            'options.*.rules' => 'nullable|array|size:2',
            'options.*.rules.*' => 'string',
            'options.*.name' => 'required_with:options|string|min:1',
            'options.*.options' => 'required_with:options|array|min:1',
            'options.*.options.*.name' => 'required|string|min:1',
            'options.*.options.*.enabled' => 'sometimes|boolean',
            'addons' => 'nullable|array',
            'addons.*.enabled' => 'sometimes|boolean',
            'addons.*.rules' => 'nullable|array|size:2',
            'addons.*.rules.*' => 'string',
            'addons.*.name' => 'required_with:addons|string|min:1',
            'addons.*.options' => 'required_with:addons|array|min:1',
            'addons.*.options.*.name' => 'required|string|min:1',
            'addons.*.options.*.price' => 'required|numeric|min:0',
            'addons.*.options.*.enabled' => 'sometimes|boolean',
        ];

        // Conditional validation based on type
        if ($this->type === 'set') {
            $rules['base_price'] = 'required|numeric|min:0|max:999999.99';
        } else {
            $rules['price'] = 'required|numeric|min:0|max:999999.99';
        }

        $messages = [
            'options.*.name.required_with' => 'The options field is required.',
            'options.*.options.required_with' => 'The options field is required.',
            'options.*.options.min' => 'The options field is required.',
            'options.*.options.*.name.required' => 'The options field is required.',

            'addons.*.name.required_with' => 'The addons field is required.',
            'addons.*.options.required_with' => 'The addons field is required.',
            'addons.*.options.min' => 'The addons field is required.',
            'addons.*.options.*.name.required' => 'The addons field is required.',
            'addons.*.options.*.price.required' => 'The addons field is required.',
        ];

        $validated = $this->validate($rules, $messages);


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

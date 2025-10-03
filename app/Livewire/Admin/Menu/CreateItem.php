<?php

namespace App\Livewire\Admin\Menu;

use App\Models\Category;
use App\Models\MenuItem;
use App\Services\Admin\StoreService;
use Illuminate\Validation\Rules\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class CreateItem extends Component
{
    use WithFileUploads;

    public string $name = '';
    public ?string $description = null;
    public $price;
    public $base_price;
    public ?int $category_id = null;
    public bool $is_active = true;
    public bool $enabled = true;
    public int $stock = 0;
    public ?string $tag = null; // popular | bestseller
    public ?string $type = 'ala_carte'; // set | ala_carte
    public array $options = [];
    public array $addons = [];
    public $image;
    public $currentStore;

    public function mount()
    {
        $storeService = app(StoreService::class);
        $this->currentStore = $storeService->getCurrentStore();
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
            'options.*.name' => 'required_with:options|string|min:1',
            'options.*.options' => 'required_with:options|array|min:1',
            'options.*.options.*.name' => 'required|string|min:1',
            'addons' => 'nullable|array',
            'addons.*.name' => 'required_with:addons|string|min:1',
            'addons.*.options' => 'required_with:addons|array|min:1',
            'addons.*.options.*.name' => 'required|string|min:1',
            'addons.*.options.*.price' => 'required|numeric|min:0',
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

        $item = new MenuItem($validated);
        $item->store_id = $this->currentStore->id;

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
            'categories' => Category::where('store_id', $this->currentStore->id)->orderBy('name')->get(['id', 'name']),
            'isEdit' => false,
            'menuItem' => null,
            'navigationBar' => true,
            'showBackButton' => true,
            'pageTitle' => 'Create New Menu Item',
            'breadcrumbs' => [
                ['label' => 'Menu', 'url' => route('admin.menu.index')],
                ['label' => 'Create Item']
            ],
            'actionButtons' => [
                [
                    'type' => 'link',
                    'label' => 'View All Items',
                    'url' => route('admin.menu.index'),
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>'
                ],
                [
                    'type' => 'button',
                    'label' => 'Create Item',
                    'onclick' => '$wire.save()',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>'
                ]
            ]
        ]);
    }
}

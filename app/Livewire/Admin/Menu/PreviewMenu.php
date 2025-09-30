<?php

namespace App\Livewire\Admin\Menu;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Store;
use App\Services\StoreService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class PreviewMenu extends Component
{
    public string $search = '';
    public ?int $categoryId = null;
    public ?Store $currentStore = null;

    public function mount(): void
    {
        $storeService = app(StoreService::class);
        $this->currentStore = $storeService->getCurrentStore();
    }

    public function getCategoriesProperty()
    {
        if (!$this->currentStore) {
            return collect();
        }

        return Category::where('is_active', true)
            ->where('parent_id', null)
            ->where('store_id', $this->currentStore->id)
            ->ordered()
            ->get(['id', 'name']);
    }

    public function getItemsProperty()
    {
        if (!$this->currentStore) {
            return collect();
        }

        $query = MenuItem::query()
            ->where('is_active', true)
            ->where('store_id', $this->currentStore->id)
            ->when($this->categoryId, function ($q) {
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
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            // In-stock items first, then by position
            ->orderByRaw('(CASE WHEN COALESCE(stock, 0) > 0 THEN 0 ELSE 1 END)')
            ->orderBy('position');

        $items = $query->get(['id', 'category_id', 'name', 'description', 'price', 'base_price', 'type', 'options', 'addons', 'image_path', 'is_active', 'stock', 'tag']);

        // Sanitize: hide disabled groups, but keep disabled options visible (UI disables inputs)
        return $items->map(function (MenuItem $item) {
            $options = is_array($item->options) ? $item->options : [];
            $addons = is_array($item->addons) ? $item->addons : [];

            // Filter option groups by enabled=true (default true). Keep all options (enabled flag used in view)
            $options = collect($options)
                ->filter(fn($group) => ($group['enabled'] ?? true) === true)
                ->map(function ($group) {
                    $group['options'] = array_values($group['options'] ?? []);
                    return $group;
                })
                ->values()->all();

            // Filter addon groups by enabled=true (default true). Keep all addon options (enabled flag used in view)
            $addons = collect($addons)
                ->filter(fn($group) => ($group['enabled'] ?? true) === true)
                ->map(function ($group) {
                    $group['options'] = array_values($group['options'] ?? []);
                    return $group;
                })
                ->values()->all();

            $item->setAttribute('options', $options);
            $item->setAttribute('addons', $addons);
            return $item;
        });
    }

    public function render()
    {
        return view('livewire.admin.menu.preview-menu', [
            'navigationBar' => true,
            'showBackButton' => true,
            'pageTitle' => 'Preview Menu',
            'breadcrumbs' => [
                ['label' => 'Menu', 'url' => route('admin.menu.index')],
                ['label' => 'Preview']
            ],
            'actionButtons' => [
                [
                    'type' => 'link',
                    'label' => 'View Customer Menu',
                    'url' => route('menu.store', ['store' => $this->currentStore?->slug]),
                    'target' => '_blank',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>'
                ]
            ]
        ]);
    }
}

<?php

namespace App\Services\Admin;

use App\Models\Store;
use App\Models\Category;
use App\Models\MenuItem;

class OnboardingService
{
    protected Store $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Define all onboarding steps
     */
    public function getSteps(): array
    {
        return [
            'store_details' => [
                'title' => 'Complete Store Details',
                'description' => 'Add your store information, address, and contact details',
                'route' => 'admin.settings.store-details',
                'completed' => $this->isStoreDetailsComplete(),
                'icon' => 'store'
            ],
            'store_hours' => [
                'title' => 'Set Store Hours',
                'description' => 'Configure your operating hours and availability',
                'route' => 'admin.settings.store-hours',
                'completed' => $this->isStoreHoursComplete(),
                'icon' => 'clock'
            ],
            'store_address' => [
                'title' => 'Add Store Address',
                'description' => 'Set up your store location and delivery area',
                'route' => 'admin.settings.store-address',
                'completed' => $this->isStoreAddressComplete(),
                'icon' => 'location'
            ],
            'store_media' => [
                'title' => 'Upload Store Media',
                'description' => 'Add your store logo and cover images',
                'route' => 'admin.settings.store-media',
                'completed' => $this->isStoreMediaComplete(),
                'icon' => 'image'
            ],
            'create_categories' => [
                'title' => 'Create Menu Categories',
                'description' => 'Add categories to organize your menu items',
                'route' => 'admin.categories.index',
                'completed' => $this->isCategoriesComplete(),
                'icon' => 'folder'
            ],
            'add_menu_items' => [
                'title' => 'Add Menu Items',
                'description' => 'Create your first menu items with prices and descriptions',
                'route' => 'admin.menu.index',
                'completed' => $this->isMenuItemsComplete(),
                'icon' => 'menu'
            ],
            'test_order' => [
                'title' => 'Test Order Flow',
                'description' => 'Place a test order to ensure everything works correctly',
                'route' => 'menu.store.index',
                'route_params' => ['store' => $this->store->slug],
                'completed' => $this->isTestOrderComplete(),
                'icon' => 'shopping-cart'
            ]
        ];
    }

    /**
     * Check if all onboarding steps are completed
     */
    public function isOnboardingComplete(): bool
    {
        $steps = $this->getSteps();

        foreach ($steps as $step) {
            if (!$step['completed']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage(): int
    {
        $steps = $this->getSteps();
        $completedSteps = array_filter($steps, fn($step) => $step['completed']);

        return round((count($completedSteps) / count($steps)) * 100);
    }

    /**
     * Get next incomplete step
     */
    public function getNextStep(): ?array
    {
        $steps = $this->getSteps();

        foreach ($steps as $key => $step) {
            if (!$step['completed']) {
                return array_merge($step, ['key' => $key]);
            }
        }

        return null;
    }

    /**
     * Mark onboarding as complete
     */
    public function completeOnboarding(): void
    {
        $this->store->update(['is_onboarding' => false]);
    }

    /**
     * Reset onboarding status
     */
    public function resetOnboarding(): void
    {
        $this->store->update(['is_onboarding' => true]);
    }

    /**
     * Check if store details are complete
     */
    protected function isStoreDetailsComplete(): bool
    {
        return !empty($this->store->name) &&
            !empty($this->store->description) &&
            !empty($this->store->phone) &&
            !empty($this->store->email);
    }

    /**
     * Check if store hours are complete
     */
    protected function isStoreHoursComplete(): bool
    {
        // Check if store has operating hours set
        // This would depend on your store hours implementation
        return true; // Placeholder - implement based on your store hours logic
    }

    /**
     * Check if store address is complete
     */
    protected function isStoreAddressComplete(): bool
    {
        return !empty($this->store->address_line1) &&
            !empty($this->store->city) &&
            !empty($this->store->state) &&
            !empty($this->store->postal_code);
    }

    /**
     * Check if store media is complete
     */
    protected function isStoreMediaComplete(): bool
    {
        return !empty($this->store->logo_path);
    }

    /**
     * Check if categories are complete
     */
    protected function isCategoriesComplete(): bool
    {
        return Category::where('store_id', $this->store->id)
            ->where('is_active', true)
            ->count() > 0;
    }

    /**
     * Check if menu items are complete
     */
    protected function isMenuItemsComplete(): bool
    {
        return MenuItem::where('store_id', $this->store->id)
            ->where('is_active', true)
            ->count() >= 3; // Require at least 3 menu items
    }

    /**
     * Check if test order is complete
     */
    protected function isTestOrderComplete(): bool
    {
        // This could check if there's at least one order placed
        // For now, we'll consider it complete if menu items exist
        return $this->isMenuItemsComplete();
    }
}

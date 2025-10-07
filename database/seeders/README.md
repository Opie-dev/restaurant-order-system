# Database Seeders Documentation

This document describes the database seeders available for the restaurant ordering system.

## Available Seeders

### 1. ComprehensiveSeeder
**File**: `database/seeders/ComprehensiveSeeder.php`

Creates a complete dataset with multiple stores, merchants, customers, and realistic data.

**What it creates:**
- **5 Merchants** with different restaurant types
- **5 Stores** (one per merchant) with different cuisines and opening hours
- **Multiple Categories** per store (7-8 categories each)
- **Menu Items** with random options and addons (3-8 items per category)
- **25 Customers** with realistic data
- **Addresses** for each customer (1-3 addresses each)
- **Orders** for each store (15-30 orders per store)
- **Cart Items** for customers (0-5 items per customer)

**Store Types Created:**
- Ahmad Restaurant - Main Branch (Traditional Malaysian)
- Sarah Cafe - Downtown (Coffee & Light Meals)
- Hassan Food - Subang (Halal Fast Food)
- Fatima Kitchen - PJ (Home-style Cooking)
- Omar Bistro - KLCC (Upscale International)

### 2. BasicSeeder
**File**: `database/seeders/BasicSeeder.php`

Creates minimal data for testing and development.

**What it creates:**
- Default admin user (`admin@example.com`)
- Default customer user (`customer@example.com`)
- One store with basic menu items
- Sample orders and addresses

### 3. Individual Seeders
- `StoreSeeder.php` - Creates stores and assigns existing data
- `MenuItemSeeder.php` - Creates menu items with options and addons
- `OrderSeeder.php` - Creates orders with realistic selections
- `OrderItemSeeder.php` - Creates order items
- `UserAddressSeeder.php` - Creates addresses for users

## Usage

### Run Comprehensive Seeding (Default)
```bash
php artisan db:seed
```
This runs the `DatabaseSeeder` which calls `ComprehensiveSeeder`.

### Run Basic Seeding Only
```bash
php artisan db:seed --class=BasicSeeder
```

### Run Individual Seeders
```bash
php artisan db:seed --class=StoreSeeder
php artisan db:seed --class=MenuItemSeeder
php artisan db:seed --class=OrderSeeder
```

### Fresh Migration with Comprehensive Data
```bash
php artisan migrate:fresh --seed
```

### Fresh Migration with Basic Data
```bash
php artisan migrate:fresh --seed --seeder=BasicSeeder
```

## Data Structure

### Menu Items with Options and Addons

The comprehensive seeder creates menu items with realistic options and addons:

**Options Examples:**
- Size: Small, Medium, Large
- Spice Level: Mild, Medium, Hot, Extra Hot
- Cooking Style: Grilled, Fried, Steamed, Baked
- Protein Choice: Chicken, Beef, Fish, Vegetarian

**Addons Examples:**
- Extra Toppings: Extra Cheese (+RM1-3), Extra Meat (+RM2-5)
- Sides: French Fries (+RM2-4), Onion Rings (+RM2.50-4.50)
- Beverages: Soft Drink (+RM2-3), Fresh Juice (+RM3-5)

### Order Selections

Orders are created with realistic selections based on menu item options:
- Required options are always selected
- Optional options are randomly selected
- Addons are randomly added with pricing
- Order totals include base price + addon prices + tax + delivery fee

### Store Settings

Each store has realistic opening hours:
- Different operating hours for each store
- Some stores closed on Sundays
- Different peak hours and closing times
- Realistic phone numbers and addresses

## Test Data Credentials

### Default Users (Created by both seeders)
- **Admin**: `admin@example.com` / `password`
- **Customer**: `customer@example.com` / `password`

### Comprehensive Seeder Merchants
- **Ahmad Restaurant**: `ahmad@restaurant.com` / `password`
- **Sarah Cafe**: `sarah@cafe.com` / `password`
- **Hassan Food**: `hassan@food.com` / `password`
- **Fatima Kitchen**: `fatima@kitchen.com` / `password`
- **Omar Bistro**: `omar@bistro.com` / `password`

## Customization

### Adding More Stores
Edit `ComprehensiveSeeder.php` and add more entries to the `$storeData` array.

### Adding More Categories
Modify the `$categoryTemplates` array in the `createCategories()` method.

### Adding More Menu Item Types
Extend the `generateRandomOptions()` and `generateRandomAddons()` methods.

### Changing Data Volume
Modify the counts in the seeder methods:
- Customer count: Change the loop in `createCustomers()`
- Order count per store: Change `$orderCount` in `createOrders()`
- Menu items per category: Change `$itemCount` in `createMenuItems()`

## Performance Notes

- **Comprehensive Seeder**: Creates ~500+ records, takes 30-60 seconds
- **Basic Seeder**: Creates ~50 records, takes 5-10 seconds
- **Memory Usage**: Comprehensive seeder uses ~50MB peak memory
- **Database Size**: Comprehensive data creates ~2-5MB database

## Troubleshooting

### Common Issues

1. **Unique Constraint Violations**
   - Run `php artisan migrate:fresh --seed` to start clean
   - Check for existing data before seeding

2. **Memory Issues**
   - Increase PHP memory limit: `php -d memory_limit=512M artisan db:seed`
   - Run individual seeders instead of comprehensive

3. **Foreign Key Constraints**
   - Ensure migrations are run before seeding
   - Check that referenced models exist

### Debugging

Enable verbose output:
```bash
php artisan db:seed -v
```

Check specific seeder:
```bash
php artisan db:seed --class=ComprehensiveSeeder -v
```

## Data Relationships

The seeders maintain proper relationships:
- Stores belong to merchants (admin users)
- Categories belong to stores
- Menu items belong to stores and categories
- Orders belong to stores and customers
- Order items belong to orders and menu items
- Cart items belong to customers and menu items
- Addresses belong to customers

This ensures data integrity and realistic testing scenarios.

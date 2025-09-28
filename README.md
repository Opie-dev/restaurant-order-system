## Gourmet Express – Restaurant Ordering System (Laravel 11 + Livewire 3)

Gourmet Express is a full-featured restaurant ordering app built with Laravel 11, Livewire v3, Alpine.js v3 and Tailwind CSS. It supports customer ordering, carts, checkout with delivery/self‑pickup, order history, address management, and an admin back office for managing menu, availability, and orders. Payments integrate with Stripe (webhooks ready design).

### Tech Stack
- Laravel 11 (PHP 8.2+)
- Livewire 3 + Alpine.js 3
- Tailwind CSS
- MySQL/PostgreSQL
- Queues: database (default)
- Cache: redis/file
- Storage: public disk (storage/app/public)

### Key Features

#### 1) Authentication & Roles
- Breeze-style auth with Livewire pages for Login and Register
- Roles on `users.role`: `admin` and `customer`
- Guarded admin routes with `role:admin`
- Minimal `layouts/auth` for auth pages (header removed on auth screens)

#### 2) Customer Experience
- Responsive customer layout (`resources/views/layouts/customer.blade.php`)
  - Modern header with brand “Gourmet Express”, user dropdown (orders, addresses, logout)
  - Cart link with a live quantity badge
  - Auto-dismissing toast notifications with progress and error styling
- Menu page (Livewire): side-by-side menu and order panels on desktop, responsive on mobile
  - Category filter chips, search with live updates
  - Menu item cards with image, price, description, and full-width “Add to Order”
  - “Popular/Bestseller” tags if configured by admin
  - Out-of-stock handling (disabled add, blocked +/- updates, clear messaging)
- Cart panel
  - Horizontal cart items with small image, quantity controls, remove, and line totals
  - Totals with tax (8%) and delivery fee note
  - Proceed to checkout and clear order actions
- Checkout page
  - Choose Delivery or Self-pickup (Self-pickup default)
  - If Delivery: shows default address or prompt to add
  - Order Summary with thumbnails, qty × unit price, line totals, and notes field
  - Delivery fee note shown only when Delivery is selected
- Order History (customer)
  - Search by order code and filter by payment status
  - Shows latest 5 orders, sorted by newest

#### 3) Addresses (Customer)
- Add/Edit/Delete addresses
- Set default address (used during checkout when Delivery is enabled)
- Phone is required; tidy dropdown/accordion UI for saved addresses

#### 4) Cart & Checkout Logic
- `CartService` provides: current cart retrieval (user/guest), add/increment/decrement/remove/clear
- Quantity rules
  - Respect item `stock` when adding/incrementing
  - When `stock` is 0: no-op on +/- with a red error toast, and controls are disabled in UI
  - Minimum quantity is 1; removing requires explicit remove action
- Checkout guards
  - Blocks when cart is empty
  - Validates items have stock > 0 and quantities do not exceed stock; shows red toast with affected items

#### 5) Admin Back Office
- **Category Management**: Hierarchical category system with dropdown/accordion UI
  - Tree structure with expand/collapse functionality
  - Clickable rows for parent categories with children
  - Search functionality with real-time filtering
  - Clean table layout with status indicators and action buttons
- **Menu Management**: Enhanced category filtering with dropdown interface
  - Option groups for parent-child category relationships
  - Clickable group headers for quick parent category selection
  - Hierarchical dropdown with visual tree structure
  - Space-efficient design replacing horizontal category chips
- Item tags: `popular` or `bestseller` (show on customer/admin cards)
- Orders list (admin)
  - Search by code, filter by payment status
  - Paginated (10/page), results summary (e.g., "Showing 1 to 10 of 20 results")
  - Expandable rows (Alpine.js) with smooth transitions to view order items
  - Clear filters button and instant search

#### 6) Database Models (high level)
- User: `name, email, password, role` (with soft deletes)
- Category: Hierarchical structure with `parent_id`, `position`, `is_active` for tree organization (with soft deletes)
- MenuItem (`price`, `image_path`, `is_active`, optional `tag`, optional `stock`) (with soft deletes)
- DailyMenuAvailability (optional for future daily toggling)
- Order (`status`, `subtotal`, `tax`, `total`, `payment_status`, `payment_provider`, `payment_ref`, `notes`, `delivery_fee`, `tracking_url`, `cancellation_remarks`) (with soft deletes)
- OrderItem (`name_snapshot`, `unit_price`, `qty`, `line_total`, `selections`) (with soft deletes)
- Payment (Stripe) – design ready
- UserAddress (`label`, `recipient_name`, `phone`, address fields, `is_default`) (with soft deletes)

#### 7) Payments (Stripe) – Designed
- Create PaymentIntent during checkout (not fully wired for demo)
- Webhook `/webhooks/stripe` (to be implemented) to update `payments` and `orders`
- `payment_intent.succeeded` → mark paid + confirm order; `payment_intent.payment_failed` → mark failed
- Idempotency keys and signature verification recommended

#### 8) Toast Notifications
- Alpine-based toast with auto-dismiss timer and visual progress bar
- Error messages show a red theme; success/informational use dark theme
- Supports both string payloads and structured `{ type, message }`

#### 9) Soft Deletes
- All major models implement soft deletes for data preservation
- Models with soft deletes: User, Category, MenuItem, Order, OrderItem, UserAddress
- Records are marked as deleted instead of permanently removed
- Supports recovery with `restore()` and permanent deletion with `forceDelete()`
- Queries automatically exclude soft-deleted records by default
- Use `withTrashed()` to include deleted records, `onlyTrashed()` for deleted-only queries

### Primary Routes
- Public/Customer
  - `/menu` – Livewire customer menu
  - `/cart` – Cart page
  - `/checkout` – Checkout
  - `/orders` – Customer order history (auth)
  - `/addresses` – Manage customer addresses (auth)
- Auth
  - `/login`, `/register` – Livewire auth
- Admin (role:admin)
  - `/admin/menu`, `/admin/menu/create`, `/admin/menu/{id}/edit`
  - `/admin/categories`
  - `/admin/orders`

### Notable UX Details
- Consistent Tailwind styling; focus rings and borders improved for visibility
- Mobile-first responsive design across pages
- Header and content alignment consistent; single customer layout
- Cart badge with white ring and "99+" cap for large quantities
- **Admin Category Management**: Intuitive tree view with clickable rows and smooth expand/collapse animations
- **Category Dropdown**: Space-efficient option groups with hierarchical visual structure
- **Interactive Elements**: Hover effects, smooth transitions, and clear visual feedback throughout admin interfaces

### Getting Started
1) Clone and install
```
composer install
npm install
npm run build   # or: npm run dev
cp .env.example .env
php artisan key:generate
```
2) Configure `.env`
- Database connection
- Queue, cache
- Filesystem public disk (run `php artisan storage:link`)
- Stripe keys if integrating payments

3) Migrate and seed
```
php artisan migrate
php artisan db:seed
```

4) Run the app
```
php artisan serve
```

### Developer Notes
- Livewire components live in `app/Livewire/**`; Blade in `resources/views/livewire/**`
- Customer layout: `resources/views/layouts/customer.blade.php`
- Error handling aims for early returns and clear user feedback
- Code style targets PSR-12; prefer explicit typing and meaningful names
- Soft deletes are implemented across all major models for data integrity and recovery

### Roadmap / Ideas
- Stripe PaymentIntent creation on checkout confirm + webhook handling
- Daily availability toggle UI and enforcement
- Realtime order status updates (broadcasting/events) for order tracking page
- Role-based policies for admin actions

—

If something looks off or you’d like a tweak, open an issue or describe the change you want and we’ll adjust it quickly.



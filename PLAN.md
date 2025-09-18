## Restaurant Order System Plan (Laravel + Livewire + Alpine.js)

### High-level goals
- **Tech stack**: Laravel 11, Livewire v3, Alpine.js v3, Tailwind CSS
- **Roles**: Admin and Customer
- **Admin**: Manage menu (CRUD), control daily availability, monitor orders, view payments
- **Customer**: Browse catalog, place order, pay online, track order status

### Architecture
- **Auth**: `laravel/breeze` with `role` field (`admin`, `customer`)
- **Payments**: Stripe (or local gateway) with webhooks
- **Queues**: `database` driver for mails/webhooks; **Cache**: `redis` or `file`
- **Database**: MySQL/PostgreSQL

### Core domain model
- **Users**: `id, name, email, role`
- **Categories**: `id, name, is_active`
- **MenuItems**: `id, category_id, name, description, price, image_path, is_active`
- **DailyMenuAvailability**: `id, menu_item_id, date, is_enabled` (per-day override)
- **Orders**: `id, user_id (nullable), code, status, subtotal, tax, total, payment_status, payment_provider, payment_ref, notes`
- **OrderItems**: `id, order_id, menu_item_id, name_snapshot, unit_price, qty, line_total`
- **Payments**: `id, order_id, provider, amount, currency, status, external_id, payload`
- Optional: **Addresses** or **TableNo**, **Settings** (tax rate, opening hours)

### Order and payment states
- **Order status**: `pending` → `confirmed` → `preparing` → `ready` → `completed` (or `cancelled`)
- **Payment status**: `unpaid` → `processing` → `paid` → `refunded` → `failed`
- Webhooks update payment; order transitions guarded by business rules

### Customer flow (Livewire components)
- **Catalog page**
  - Components: `Catalog/Filters`, `Catalog/Grid`, `Catalog/Search`
  - Only show items with `is_active=true` and enabled for `today()` in `DailyMenuAvailability`
  - Alpine usage: quick view modal, quantity stepper
- **Cart page**
  - Component: `Cart/Show` (DB for authed users; session for guests)
  - Add/remove, update quantity, promo (optional)
- **Checkout page**
  - Component: `Checkout/Form` with customer info and notes
  - On submit: create `Order` + `OrderItems`, compute totals, create payment intent
  - Redirect to hosted payment or render inline payment element
- **Order status page**
  - Component: `Order/Track` (polling or Livewire events) for live updates
- **Receipt page**
  - Component: `Order/Receipt` after successful payment

### Admin flow (Livewire components)
- **Dashboard**
  - KPIs: today’s orders, revenue, pending orders
  - Real-time list of `pending/confirmed` with actions
- **Menu management (CRUD)**
  - Components: `Admin/Menu/List`, `Admin/Menu/Form`, `Admin/Categories/List`
  - Toggle `is_active`; upload images (filesystem or Spatie Media Library)
- **Daily availability control**
  - Component: `Admin/Availability/Calendar` to enable/disable items for a date
- **Orders management**
  - Component: `Admin/Orders/List` (filters by status/date/user)
  - `Admin/Orders/Show`: change status with guarded transitions; view timeline
- **Payments**
  - `Admin/Payments/List` with filters; link to order; refund if supported

### Pages and routes
- **Public**: `/`, `/menu`, `/cart`, `/checkout`, `/order/{code}`, `/receipt/{code}`
- **Auth**: `/login`, `/register`, `/orders` (customer history)
- **Admin** (middleware `role:admin`):
  - `/admin`, `/admin/menu`, `/admin/menu/create`, `/admin/menu/{id}/edit`
  - `/admin/categories`
  - `/admin/availability`
  - `/admin/orders`, `/admin/orders/{id}`
  - `/admin/payments`

### Livewire components (suggested structure)
- `app/Livewire/Catalog/Filters.php`
- `app/Livewire/Catalog/Grid.php`
- `app/Livewire/Cart/Show.php`
- `app/Livewire/Checkout/Form.php`
- `app/Livewire/Order/Track.php`
- `app/Livewire/Order/Receipt.php`
- `app/Livewire/Admin/Menu/ListItems.php`
- `app/Livewire/Admin/Menu/EditItem.php`
- `app/Livewire/Admin/Categories/ListCategories.php`
- `app/Livewire/Admin/Availability/Calendar.php`
- `app/Livewire/Admin/Orders/ListOrders.php`
- `app/Livewire/Admin/Orders/ShowOrder.php`
- `app/Livewire/Admin/Payments/ListPayments.php`

### Blade layout and Alpine integration
- Base layout `resources/views/layouts/app.blade.php` with Tailwind, Alpine, Livewire
- Alpine for modals, dropdowns, image preview, quantity steppers, confirmations
- Flash notifications via Alpine stores

### Payment integration (Stripe example)
- Create PaymentIntent at checkout; save `payment_ref`
- Webhook `/webhooks/stripe`:
  - `payment_intent.succeeded` → set `payments.status=paid`, `orders.payment_status=paid`, `orders.status=confirmed`
  - `payment_intent.payment_failed` → mark failed
- Use idempotency keys and signature verification
- Optional cash on delivery: keep `payment_status=unpaid`

### Validation and guards
- Validate `is_active` and daily availability when adding to cart and before order creation
- Lock price snapshots in `OrderItems.name_snapshot` and `unit_price`
- Optional stock control: add `MenuItems.stock_qty`

### Notifications
- Email to customer on order creation and payment success
- Admin toast/notification when new pending order arrives (Livewire event broadcast)
- Optional SMS/WhatsApp (Twilio)

### Observability and operations
- Activity log for order status changes (user, timestamp, note)
- Audit trails for menu CRUD (Spatie activity-log)
- Error tracking (Sentry) and request logging
- DB and `storage/app/public` backups

### Security
- Authorization by role via middleware/Policies
- CSRF enabled; validate webhook signatures
- Rate limit add-to-cart and checkout
- Restrict admin routes to `role:admin` (separate guard optional)

### Data migrations (outline)
- `create_categories_table`
- `create_menu_items_table`
- `create_daily_menu_availability_table`
- `create_orders_table`
- `create_order_items_table`
- `create_payments_table`
- Add `role` to `users`

### Seeding
- Admin user
- Sample categories and menu items with images
- Today’s availability enabled

### Delivery phases
- **Phase 1**: Auth, Catalog, Cart, Checkout (cash only), Admin menu CRUD
- **Phase 2**: Payment gateway + webhooks, Admin orders management, status tracking
- **Phase 3**: Daily availability calendar, reporting, notifications, polish
- **Phase 4**: Optimizations, accessibility, end-to-end tests

### Testing
- Feature tests: catalog visibility, add-to-cart, checkout, webhook updates
- Unit tests: order totals, state transitions
- Browser tests: happy path purchase

# Comprehensive Test Suite for Restaurant Order System

This document outlines the comprehensive test suite created for the restaurant order system, covering both merchant and customer functionalities.

## Test Structure

All tests are organized using Pest PHP testing framework and follow Laravel testing conventions with `RefreshDatabase` trait.

## Merchant Tests

### 1. Merchant Authentication Tests (`tests/Feature/Merchant/MerchantAuthenticationTest.php`)
- **Login functionality**: Valid/invalid credentials, email format validation
- **Registration**: New merchant account creation, password confirmation, duplicate email prevention
- **Access control**: Redirects for authenticated users, logout functionality
- **Security**: Password length validation, authentication requirements

### 2. Store Management Tests (`tests/Feature/Merchant/StoreManagementTest.php`)
- **Store creation**: Basic store creation, unique slug generation, validation
- **Store selector**: Display stores, switching between stores, access control
- **Store details**: Update basic info, address management, active status toggle
- **Store relationships**: Categories, menu items, admin user relationships
- **Store availability**: Opening hours logic, always open mode, time-based availability

### 3. Store Settings Tests (`tests/Feature/Merchant/StoreSettingsTest.php`)
- **Store details settings**: Basic info updates, logo/cover uploads, validation
- **Store address settings**: Address management, validation, formatted address display
- **Store hours settings**: Opening hours configuration, always open mode, time validation
- **Security settings**: Password updates, store deactivation, account deletion
- **Access control**: Prevents unauthorized access to other stores' settings

### 4. Menu Management Tests (`tests/Feature/Merchant/MenuManagementTest.php`)
- **Menu list**: Display items, search functionality, filtering, sorting
- **Menu item creation**: Basic item creation, options/addons, image uploads, validation
- **Menu item editing**: Updates, category changes, active status, options management
- **Menu availability**: Active/inactive items, store availability impact
- **Access control**: Prevents editing other stores' menu items

### 5. Category Management Tests (`tests/Feature/Merchant/CategoryManagementTest.php`)
- **Category list**: Display categories, search, filtering, sorting, item counts
- **Category creation**: Basic creation, validation, image uploads, display order
- **Category editing**: Updates, active status, image changes, validation
- **Category relationships**: Store relationships, menu items, active items
- **Display order**: Reordering, moving up/down, bulk operations
- **Access control**: Prevents unauthorized access

### 6. Kitchen Management Tests (`tests/Feature/Merchant/KitchenManagementTest.php`)
- **Active orders display**: Chronological order, order details, timing information
- **Order status updates**: Status transitions, validation, priority management
- **Order filtering**: Status filters, time ranges, search functionality
- **Order refresh**: Manual refresh, auto-refresh, last update time
- **Order priority**: Urgent orders, priority indicators, priority changes
- **Notifications**: Status change notifications, SMS for urgent orders
- **Access control**: Admin-only access, store-specific orders

### 7. Order Management Tests (`tests/Feature/Merchant/OrderManagementTest.php`)
- **Order list**: Display orders, filtering, searching, sorting, export
- **Order details**: Order information, totals, items, status updates
- **Order statistics**: Revenue tracking, completion rates, growth metrics
- **Order notifications**: Status changes, cancellations, refunds
- **Access control**: Admin-only access, store-specific orders

### 8. Customer Management Tests (`tests/Feature/Merchant/CustomerManagementTest.php`)
- **Customer list**: Display customers, search, filtering, statistics
- **Customer details**: Customer info, order history, address management
- **Customer statistics**: Order counts, total spent, retention rates
- **Access control**: Admin-only access, store-specific customers

### 9. Onboarding Flow Tests (`tests/Feature/Merchant/OnboardingFlowTest.php`)
- **Onboarding steps**: Welcome, store details, address, hours, categories, menu items
- **Step validation**: Required fields, data validation, progress saving
- **Onboarding completion**: Summary display, welcome email, dashboard redirect
- **Access control**: Admin-only access, onboarding state management

## Customer Tests

### 1. Customer Authentication Tests (`tests/Feature/Customer/CustomerAuthenticationTest.php`)
- **Login functionality**: Valid/invalid credentials, existing/non-existing customers
- **Registration**: New customer accounts, validation, duplicate prevention
- **Order history**: View orders, order details, access control
- **Profile management**: Profile updates, address management
- **Access control**: Customer area access, admin area restrictions

### 2. Cart Functionality Tests (`tests/Feature/Customer/CartFunctionalityTest.php`)
- **Add to cart (guest)**: Basic items, options, addons, quantity updates
- **Add to cart (authenticated)**: User-specific carts, persistence, merging
- **Cart management**: Update quantities, remove items, clear cart, totals
- **Cart validation**: Required options, quantity limits, availability checks
- **Cart persistence**: Session storage, checkout clearing, store switching

### 3. Checkout Flow Tests (`tests/Feature/Customer/CheckoutFlowTest.php`)
- **Guest checkout**: Required information, validation, order creation
- **Authenticated checkout**: Saved addresses, new addresses, order creation
- **Payment processing**: Card payments, cash payments, validation
- **Order creation**: Totals calculation, order items, unique codes, status setting
- **Notifications**: Order confirmation, admin notifications, SMS notifications
- **Validation**: Empty cart prevention, store availability, payment failures

## Store Availability Tests

### Store Availability Tests (`tests/Feature/StoreAvailabilityTest.php`)
- **Store active/inactive**: Menu visibility, cart prevention, checkout prevention
- **Opening hours**: Time-based availability, next opening time, disabled days
- **Always open mode**: 24/7 availability, checkout availability
- **Menu item availability**: Active/inactive items, out of stock messages
- **Edge cases**: No hours set, invalid times, missing settings, timezone handling
- **Notifications**: Status change notifications, customer notifications
- **API endpoints**: Availability API, hours API, menu availability API

## Test Coverage Areas

### Authentication & Authorization
- User login/logout for both merchants and customers
- Role-based access control
- Session management
- Password validation and security

### Store Management
- Store creation and configuration
- Store settings management
- Store availability and hours
- Store relationships and data integrity

### Menu & Category Management
- CRUD operations for menu items and categories
- Image uploads and file management
- Search and filtering functionality
- Display order and organization

### Order Management
- Order creation and processing
- Status management and transitions
- Payment processing and validation
- Order tracking and notifications

### Customer Experience
- Cart functionality and persistence
- Checkout flow and validation
- Order history and tracking
- Profile and address management

### Kitchen Operations
- Order display and management
- Status updates and transitions
- Priority management
- Real-time updates and notifications

### Data Validation & Security
- Input validation and sanitization
- Access control and authorization
- Data integrity and relationships
- Error handling and edge cases

## Test Implementation Notes

1. **Framework**: Uses Pest PHP for readable test syntax
2. **Database**: All tests use `RefreshDatabase` for clean state
3. **Authentication**: Uses `actingAs()` for user authentication in tests
4. **Livewire**: Tests Livewire components with proper assertions
5. **File Uploads**: Tests file upload functionality with fake files
6. **Notifications**: Uses `Notification::fake()` for testing email/SMS
7. **Time Mocking**: Uses `travelTo()` for testing time-dependent functionality

## Missing Components (Expected)

The tests reference several Livewire components and services that may not exist yet:
- Admin Livewire components for various management areas
- Customer Livewire components for cart and checkout
- Mail notification classes
- Payment service classes
- SMS notification services

These tests serve as a comprehensive specification for the required functionality and can guide the implementation of missing components.

## Running the Tests

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test tests/Feature/Merchant/
php artisan test tests/Feature/Customer/
php artisan test tests/Feature/StoreAvailabilityTest.php

# Run with coverage
php artisan test --coverage
```

This comprehensive test suite ensures thorough coverage of all major functionality in the restaurant order system, providing confidence in the system's reliability and correctness.

# Table QR System Implementation Plan

## Overview
Implement a table QR code system that allows customers to scan QR codes at restaurant tables to access table-specific ordering. Orders are linked to table numbers for kitchen management and service tracking.

## Current System Analysis
- ✅ Multi-store architecture (Store model)
- ✅ Order system with delivery/pickup options
- ✅ Cart system with guest/user support
- ✅ Admin interface for menu management
- ❌ No table management system
- ❌ No QR code functionality
- ❌ Orders not linked to tables

## Database Schema Design

### Tables Table
```sql
CREATE TABLE tables (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    store_id BIGINT NOT NULL,
    table_number VARCHAR(10) NOT NULL,
    capacity INT DEFAULT 2,
    is_active BOOLEAN DEFAULT true,
    location_description VARCHAR(255) NULL, -- e.g., "Near window", "Patio"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    UNIQUE KEY unique_store_table (store_id, table_number),
    INDEX idx_store_active (store_id, is_active)
);
```

### Table QR Codes Table
```sql
CREATE TABLE table_qr_codes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    table_id BIGINT NOT NULL,
    qr_code VARCHAR(255) UNIQUE NOT NULL, -- Generated QR code identifier
    qr_url VARCHAR(500) NOT NULL, -- Full URL for QR scanning
    is_active BOOLEAN DEFAULT true,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL, -- Optional expiration
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
    INDEX idx_qr_code (qr_code),
    INDEX idx_table_active (table_id, is_active)
);
```

### Orders Table Extension
```sql
-- Add table reference to existing orders table
ALTER TABLE orders ADD COLUMN table_id BIGINT NULL AFTER store_id;
ALTER TABLE orders ADD COLUMN table_number VARCHAR(10) NULL AFTER table_id; -- Snapshot for display

ALTER TABLE orders ADD FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE SET NULL;
ALTER TABLE orders ADD INDEX idx_table_orders (table_id, created_at);
```

### Current Orders Table Analysis
**Current Structure**:
- Order Code, Customer, Status, Payment, Items, Delivery Fee, Total, Date
- No table information displayed
- No table relationships

**Required Updates**:
- Add `table_id` and `table_number` fields
- Add table relationship to Order model
- Update admin interface to show table information
- Add table filtering and grouping
- Update kitchen display for table-specific orders

## Eloquent Models

### Table Model
```php
<?php
// app/Models/Table.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'table_number',
        'capacity',
        'is_active',
        'location_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(TableQrCode::class);
    }

    public function activeQrCode(): HasMany
    {
        return $this->hasMany(TableQrCode::class)->where('is_active', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "Table {$this->table_number}";
    }

    public function getCurrentOrderAttribute(): ?Order
    {
        return $this->orders()
            ->whereIn('status', ['pending', 'preparing', 'delivering'])
            ->latest()
            ->first();
    }
}
```

### Updated Order Model
```php
<?php
// app/Models/Order.php - Updated with table support

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    // Order status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PREPARING = 'preparing';
    const STATUS_DELIVERING = 'delivering';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment status constants
    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PROCESSING = 'processing';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    const PAYMENT_STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'store_id',
        'table_id',        // NEW
        'table_number',    // NEW
        'address_id',
        'code',
        'status',
        'subtotal',
        'tax',
        'tax_rate',
        'total',
        'payment_status',
        'payment_provider',
        'payment_ref',
        'tracking_url',
        'delivery_fee',
        'notes',
        'cancellation_remarks',
        'ship_recipient_name',
        'ship_phone',
        'ship_line1',
        'ship_line2',
        'ship_city',
        'ship_state',
        'ship_postal_code',
        'ship_country',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'order_items')
            ->withPivot(['name_snapshot', 'unit_price', 'qty', 'line_total'])
            ->withTimestamps();
    }

    /**
     * Get order type (table, delivery, pickup)
     */
    public function getOrderTypeAttribute(): string
    {
        if ($this->table_id) {
            return 'table';
        } elseif ($this->delivery_fee > 0) {
            return 'delivery';
        } else {
            return 'pickup';
        }
    }

    /**
     * Get order type display text
     */
    public function getOrderTypeDisplayAttribute(): string
    {
        return match ($this->order_type) {
            'table' => "Table {$this->table_number}",
            'delivery' => 'Delivery',
            'pickup' => 'Pickup',
            default => 'Unknown'
        };
    }

    /**
     * Get order type color class for UI
     */
    public function getOrderTypeColorClassAttribute(): string
    {
        return match ($this->order_type) {
            'table' => 'bg-blue-100 text-blue-800',
            'delivery' => 'bg-green-100 text-green-800',
            'pickup' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Check if order is table-based
     */
    public function isTableOrder(): bool
    {
        return !is_null($this->table_id);
    }

    /**
     * Check if order is delivery
     */
    public function isDeliveryOrder(): bool
    {
        return $this->delivery_fee > 0;
    }

    /**
     * Check if order is pickup
     */
    public function isPickupOrder(): bool
    {
        return !$this->isTableOrder() && !$this->isDeliveryOrder();
    }

    // ... existing methods (getValidTransitions, canTransitionTo, etc.)
}
```

### TableQrCode Model
```php
<?php
// app/Models/TableQrCode.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class TableQrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'qr_code',
        'qr_url',
        'is_active',
        'generated_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    public function generateQrCode(): string
    {
        return 'TBL_' . $this->table_id . '_' . uniqid();
    }

    public function generateQrUrl(): string
    {
        return route('table.menu', ['qrCode' => $this->qr_code]);
    }

    /**
     * Generate QR code image and save to storage
     */
    public function generateQrCodeImage(): string
    {
        $qrUrl = $this->generateQrUrl();
        $fileName = "qr-codes/table-{$this->table_id}-{$this->qr_code}.png";
        
        // Generate QR code as PNG
        $qrCodeImage = QrCode::size(300)
            ->format('png')
            ->errorCorrection('H')
            ->generate($qrUrl);
            
        // Save to storage
        Storage::disk('public')->put($fileName, $qrCodeImage);
        
        return $fileName;
    }

    /**
     * Get QR code image URL
     */
    public function getQrCodeImageUrl(): string
    {
        $fileName = "qr-codes/table-{$this->table_id}-{$this->qr_code}.png";
        
        if (!Storage::disk('public')->exists($fileName)) {
            $this->generateQrCodeImage();
        }
        
        return Storage::disk('public')->url($fileName);
    }

    /**
     * Generate QR code with store logo
     */
    public function generateQrCodeWithLogo(): string
    {
        $qrUrl = $this->generateQrUrl();
        $fileName = "qr-codes/table-{$this->table_id}-{$this->qr_code}-logo.png";
        
        // Get store logo path
        $logoPath = $this->table->store->logo_path;
        
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $qrCodeImage = QrCode::size(300)
                ->format('png')
                ->errorCorrection('H')
                ->merge(Storage::disk('public')->path($logoPath), 0.3, true)
                ->generate($qrUrl);
        } else {
            $qrCodeImage = QrCode::size(300)
                ->format('png')
                ->errorCorrection('H')
                ->generate($qrUrl);
        }
        
        Storage::disk('public')->put($fileName, $qrCodeImage);
        
        return $fileName;
    }

    /**
     * Generate QR code with table number overlay
     */
    public function generateQrCodeWithTableNumber(): string
    {
        $qrUrl = $this->generateQrUrl();
        $fileName = "qr-codes/table-{$this->table_id}-{$this->qr_code}-with-number.png";
        
        // Generate QR code
        $qrCodeImage = QrCode::size(300)
            ->format('png')
            ->errorCorrection('H')
            ->generate($qrUrl);
        
        // Create image with table number
        $image = imagecreatefromstring($qrCodeImage);
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Create a larger canvas to accommodate table number
        $canvasHeight = $height + 60; // Extra space for table number
        $canvas = imagecreatetruecolor($width, $canvasHeight);
        
        // Fill background with white
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        
        // Copy QR code to canvas
        imagecopy($canvas, $image, 0, 0, 0, 0, $width, $height);
        
        // Add table number text
        $black = imagecolorallocate($canvas, 0, 0, 0);
        $fontSize = 5; // GD font size
        $text = "Table {$this->table->table_number}";
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textX = ($width - $textWidth) / 2;
        $textY = $height + 25;
        
        imagestring($canvas, $fontSize, $textX, $textY, $text, $black);
        
        // Save the image
        ob_start();
        imagepng($canvas);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        Storage::disk('public')->put($fileName, $imageData);
        
        // Clean up
        imagedestroy($image);
        imagedestroy($canvas);
        
        return $fileName;
    }
}
```

## Routes Structure

### Public Routes
```php
// routes/web.php

// Table QR routes
Route::get('/table/{qrCode}', [TableController::class, 'show'])->name('table.menu');
Route::get('/table/{qrCode}/order/{orderCode}', [TableController::class, 'track'])->name('table.order');
Route::get('/table/{qrCode}/receipt/{orderCode}', [TableController::class, 'receipt'])->name('table.receipt');
```

### Admin Routes
```php
// Admin routes (role:admin middleware)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    // Table management
    Route::resource('tables', AdminTableController::class);
    Route::get('tables/{table}/qr-codes', [AdminTableController::class, 'qrCodes'])->name('admin.tables.qr-codes');
    
    // QR code management
    Route::get('qr-codes', [AdminQrController::class, 'index'])->name('admin.qr-codes');
    Route::post('qr-codes/generate', [AdminQrController::class, 'generate'])->name('admin.qr-codes.generate');
    Route::post('qr-codes/{qrCode}/regenerate', [AdminQrController::class, 'regenerate'])->name('admin.qr-codes.regenerate');
    Route::get('qr-codes/{qrCode}/print', [AdminQrController::class, 'print'])->name('admin.qr-codes.print');
    Route::get('qr-codes/bulk-print', [AdminQrController::class, 'bulkPrint'])->name('admin.qr-codes.bulk-print');
    
    // QR code downloads
    Route::get('qr-codes/{qrCode}/download', [AdminQrController::class, 'download'])->name('admin.qr-codes.download');
    Route::get('qr-codes/{qrCode}/download-pdf', [AdminQrController::class, 'downloadPdf'])->name('admin.qr-codes.download-pdf');
    Route::get('qr-codes/{qrCode}/download-with-number', [AdminQrController::class, 'downloadWithTableNumber'])->name('admin.qr-codes.download-with-number');
    Route::get('qr-codes/bulk-download', [AdminQrController::class, 'bulkDownload'])->name('admin.qr-codes.bulk-download');
    Route::get('qr-codes/bulk-download-pdf', [AdminQrController::class, 'bulkDownloadPdf'])->name('admin.qr-codes.bulk-download-pdf');
    Route::get('qr-codes/bulk-download-zip', [AdminQrController::class, 'bulkDownloadZip'])->name('admin.qr-codes.bulk-download-zip');
});
```

## Livewire Components Structure

### Admin Components
```
app/Livewire/Admin/Tables/
├── ListTables.php
├── TableForm.php
├── TableQrCodes.php
└── TableOrders.php

app/Livewire/Admin/QrCodes/
├── ListQrCodes.php
├── QrGenerator.php
├── QrPrinter.php
├── QrDownloader.php
└── QrAnalytics.php
```

### Customer Components
```
app/Livewire/Customer/Table/
├── TableMenu.php
├── TableCart.php
├── TableCheckout.php
├── TableOrder.php
└── TableReceipt.php
```

## Implementation Todo List

### Phase 1: Admin Foundation (Priority 1)
**Package Installation**
- [ ] Install `simplesoftwareio/simple-qrcode` package
- [ ] Install `barryvdh/laravel-dompdf` package
- [ ] Configure QR code storage directory
- [ ] Set up QR code generation settings

**Database & Models**
- [ ] Create `tables` table migration
- [ ] Create `table_qr_codes` table migration
- [ ] Add `table_id` and `table_number` to `orders` table
- [ ] Create `Table` Eloquent model
- [ ] Create `TableQrCode` Eloquent model with QR generation methods
- [ ] Update `Order` model with table relationships
- [ ] Create model factories and seeders

**Admin Table Management**
- [ ] Create `AdminTableController`
- [ ] Create `ListTables` Livewire component
- [ ] Create `TableForm` Livewire component
- [ ] Create admin table management views
- [ ] Add table management to admin navigation
- [ ] Implement table CRUD operations
- [ ] Add table status management (active/inactive)

### Phase 2: Admin QR Code System (Priority 2)
**QR Code Generation**
- [ ] Create `AdminQrController`
- [ ] Create `QrGenerator` Livewire component
- [ ] Create `QrCodeService` for QR generation logic
- [ ] Implement QR code image generation with `simplesoftwareio/simple-qrcode`
- [ ] Add logo embedding functionality
- [ ] Create QR code printing functionality
- [ ] Add QR code management views

**QR Code Downloads**
- [ ] Implement single QR code download (PNG)
- [ ] Implement QR code PDF download with branding
- [ ] Implement QR code with table number overlay
- [ ] Implement bulk QR code downloads
- [ ] Create ZIP download for multiple QR codes
- [ ] Add download templates and branding options

**Admin QR Management**
- [ ] Create `QrDownloader` Livewire component
- [ ] Create QR code list view with download options
- [ ] Add QR code regeneration functionality
- [ ] Implement QR code expiration system
- [ ] Add QR code usage analytics

### Phase 3: Admin Order Integration (Priority 3)
**Order Model Updates**
- [ ] Add `table_id` and `table_number` to Order model fillable
- [ ] Add table relationship to Order model
- [ ] Add order type helper methods (table, delivery, pickup)
- [ ] Add order type display and color methods

**Admin Order Interface**
- [ ] Update admin order views to show table information
- [ ] Add Table column to orders table
- [ ] Add Order Type column (Table/Delivery/Pickup)
- [ ] Add table filtering to order management
- [ ] Update order details to show table context
- [ ] Add table-specific order actions

**Kitchen Display**
- [ ] Create kitchen display with table grouping
- [ ] Add table status indicators
- [ ] Group orders by table number
- [ ] Add table-specific order actions
- [ ] Implement real-time table order updates

### Phase 4: Customer Interface (Priority 4)
**Customer QR Flow**
- [ ] Create `TableController` for public routes
- [ ] Create `TableMenu` Livewire component
- [ ] Create table-specific menu view
- [ ] Implement QR code validation
- [ ] Add table context to cart system

**Table-Specific Ordering**
- [ ] Update `CartService` to handle table context
- [ ] Create `TableCart` Livewire component
- [ ] Create `TableCheckout` Livewire component
- [ ] Update order creation to include table information
- [ ] Create table-specific order tracking

### Phase 5: Advanced Features (Priority 5)
**Analytics & Management**
- [ ] Create QR code usage analytics
- [ ] Add bulk QR code operations
- [ ] Create table occupancy tracking
- [ ] Add table capacity management
- [ ] Implement table reservation system

**Polish & Optimization**
- [ ] Mobile-responsive QR scanning
- [ ] Offline QR code validation
- [ ] Performance optimization
- [ ] Comprehensive testing
- [ ] Documentation updates

## Technical Specifications

### QR Code Package
**Package**: `simplesoftwareio/simple-qrcode`
```bash
composer require simplesoftwareio/simple-qrcode
```

**Features**:
- Simple Laravel integration with facade
- Multiple output formats (PNG, SVG, EPS)
- Logo embedding capability
- Error correction levels
- Color customization
- Active maintenance and community support

### QR Code Format
- **Structure**: `TBL_{table_id}_{unique_id}`
- **URL Format**: `https://domain.com/table/{qr_code}`
- **Expiration**: Optional, configurable per store
- **Regeneration**: Admin can regenerate codes
- **Image Storage**: `storage/app/public/qr-codes/`
- **Image Format**: PNG (300x300px, High error correction)

### Table Management
- **Numbering**: Store-specific table numbers
- **Capacity**: Configurable seating capacity
- **Location**: Optional location descriptions
- **Status**: Active/inactive table states

### Order Integration
- **Table Linking**: Orders linked to tables via `table_id`
- **Snapshot**: `table_number` stored for display
- **Status**: Table orders follow standard order flow
- **Tracking**: Table-specific order tracking
- **Order Types**: Distinguish between table, delivery, and pickup orders
- **Admin Display**: Show table information in order management
- **Kitchen Grouping**: Group orders by table for efficient service

## Security Considerations

### QR Code Security
- **Validation**: Verify QR codes exist and are active
- **Expiration**: Respect QR code expiration dates
- **Rate Limiting**: Limit QR code scan attempts
- **Access Control**: Ensure QR codes belong to correct store

### Table Access
- **Store Isolation**: Tables isolated by store
- **Admin Permissions**: Only store admins can manage tables
- **Order Privacy**: Table orders visible only to store staff

## Testing Strategy

### Unit Tests
- [ ] Table model relationships
- [ ] QR code generation and validation
- [ ] Order-table integration
- [ ] Table capacity calculations
- [ ] Order type detection (table, delivery, pickup)
- [ ] Order model table relationship
- [ ] Table-specific order methods

### Feature Tests
- [ ] QR code scanning flow
- [ ] Table-specific ordering
- [ ] Admin table management
- [ ] QR code generation and printing
- [ ] Order table integration
- [ ] Admin order table display
- [ ] Kitchen table grouping
- [ ] Order type filtering

### Browser Tests
- [ ] Mobile QR scanning
- [ ] Table ordering complete flow
- [ ] Admin table management workflow
- [ ] QR code printing functionality

## Migration Strategy

### Package Installation
```bash
composer require simplesoftwareio/simple-qrcode
composer require barryvdh/laravel-dompdf
```

### Database Migrations
1. `create_tables_table`
2. `create_table_qr_codes_table`
3. `add_table_fields_to_orders_table`

### Configuration Setup
- [ ] Create QR code storage directory: `storage/app/public/qr-codes/`
- [ ] Configure QR code generation settings
- [ ] Set up logo embedding paths

### Data Migration
- [ ] Create default tables for existing stores
- [ ] Generate initial QR codes using `simplesoftwareio/simple-qrcode`
- [ ] Update existing orders with table context (if applicable)

### Deployment Steps
1. Install QR code package
2. Run database migrations
3. Deploy new models and controllers
4. Deploy Livewire components
5. Update admin navigation
6. Generate QR codes for existing tables
7. Test QR scanning functionality

## Success Metrics

### Functional Metrics
- [ ] QR codes successfully generated for all tables
- [ ] Customers can scan QR codes and place orders
- [ ] Orders properly linked to table numbers
- [ ] Admin can manage tables and QR codes
- [ ] Kitchen display shows table-specific orders
- [ ] Order types correctly identified (table, delivery, pickup)
- [ ] Admin interface displays table information
- [ ] Orders grouped by table in kitchen display

### Performance Metrics
- [ ] QR code scan response time < 2 seconds
- [ ] Table management page load time < 3 seconds
- [ ] Order creation with table context < 1 second
- [ ] QR code generation < 5 seconds per table

### User Experience Metrics
- [ ] Mobile QR scanning success rate > 95%
- [ ] Table ordering completion rate > 90%
- [ ] Admin table management efficiency improved
- [ ] Customer satisfaction with table ordering

## Future Enhancements

### Advanced Features
- **Table Occupancy**: Real-time table status tracking
- **Reservation Integration**: Link tables to reservation system
- **Dynamic Pricing**: Table-specific pricing options
- **Service Requests**: Table-specific service requests
- **Analytics Dashboard**: Comprehensive table usage analytics

### Integration Opportunities
- **POS Integration**: Sync with existing POS systems
- **Staff Management**: Assign staff to specific tables
- **Inventory Management**: Table-specific inventory tracking
- **Customer Loyalty**: Table-based customer recognition

## Risk Mitigation

### Technical Risks
- **QR Code Conflicts**: Ensure unique QR code generation
- **Performance Impact**: Monitor database query performance
- **Mobile Compatibility**: Test across different devices
- **Offline Functionality**: Implement offline QR validation

### Business Risks
- **Customer Adoption**: Provide clear QR scanning instructions
- **Staff Training**: Train staff on table management system
- **System Downtime**: Implement fallback ordering methods
- **Data Privacy**: Ensure table order data privacy

## QR Code Service Implementation

### QrCodeService
```php
<?php
// app/Services/Admin/QrCodeService.php

namespace App\Services\Admin;

use App\Models\Table;
use App\Models\TableQrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class QrCodeService
{
    /**
     * Generate QR code for a table
     */
    public function generateForTable(Table $table): TableQrCode
    {
        // Deactivate existing QR codes for this table
        $table->qrCodes()->update(['is_active' => false]);
        
        // Create new QR code
        $qrCode = $table->qrCodes()->create([
            'qr_code' => $this->generateUniqueQrCode($table),
            'qr_url' => $this->generateQrUrl($table),
            'is_active' => true,
            'generated_at' => now(),
        ]);
        
        // Generate QR code image
        $qrCode->generateQrCodeImage();
        
        return $qrCode;
    }

    /**
     * Generate QR codes for all active tables
     */
    public function generateForAllTables(): array
    {
        $results = [];
        
        Table::where('is_active', true)->chunk(50, function ($tables) use (&$results) {
            foreach ($tables as $table) {
                $results[] = $this->generateForTable($table);
            }
        });
        
        return $results;
    }

    /**
     * Download single QR code as PNG
     */
    public function downloadQrCode(TableQrCode $qrCode): string
    {
        $fileName = "qr-codes/table-{$qrCode->table_id}-{$qrCode->qr_code}.png";
        
        if (!Storage::disk('public')->exists($fileName)) {
            $qrCode->generateQrCodeImage();
        }
        
        return Storage::disk('public')->path($fileName);
    }

    /**
     * Download QR code as PDF with branding
     */
    public function downloadQrCodePdf(TableQrCode $qrCode): string
    {
        $qrCodePath = $this->downloadQrCode($qrCode);
        
        $pdf = Pdf::loadView('admin.qr-codes.pdf-template', [
            'qrCode' => $qrCode,
            'qrCodePath' => $qrCodePath,
            'store' => $qrCode->table->store,
            'table' => $qrCode->table,
        ]);
        
        $pdfFileName = "qr-code-table-{$qrCode->table->table_number}.pdf";
        $pdfPath = storage_path("app/temp/{$pdfFileName}");
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $pdf->save($pdfPath);
        
        return $pdfPath;
    }

    /**
     * Download QR code with table number overlay
     */
    public function downloadQrCodeWithTableNumber(TableQrCode $qrCode): string
    {
        $fileName = "qr-codes/table-{$qrCode->table_id}-{$qrCode->qr_code}-with-number.png";
        
        if (!Storage::disk('public')->exists($fileName)) {
            $qrCode->generateQrCodeWithTableNumber();
        }
        
        return Storage::disk('public')->path($fileName);
    }

    /**
     * Download multiple QR codes as ZIP
     */
    public function downloadBulkQrCodes(array $qrCodes): string
    {
        $zipFileName = 'qr-codes-' . now()->format('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path("app/temp/{$zipFileName}");
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        
        foreach ($qrCodes as $qrCode) {
            $qrCodePath = $this->downloadQrCode($qrCode);
            $fileName = "table-{$qrCode->table->table_number}-{$qrCode->qr_code}.png";
            $zip->addFile($qrCodePath, $fileName);
        }
        
        $zip->close();
        
        return $zipPath;
    }

    /**
     * Generate unique QR code identifier
     */
    private function generateUniqueQrCode(Table $table): string
    {
        do {
            $qrCode = 'TBL_' . $table->id . '_' . uniqid();
        } while (TableQrCode::where('qr_code', $qrCode)->exists());
        
        return $qrCode;
    }

    /**
     * Generate QR code URL
     */
    private function generateQrUrl(Table $table): string
    {
        return route('table.menu', ['qrCode' => $this->generateUniqueQrCode($table)]);
    }
}
```

### QR Code Configuration
```php
// config/qrcode.php
<?php

return [
    'size' => 300,
    'format' => 'png',
    'error_correction' => 'H',
    'margin' => 1,
    'color' => [0, 0, 0], // RGB
    'background_color' => [255, 255, 255], // RGB
];
```

## QR Code Download Implementation

### AdminQrController Download Methods
```php
<?php
// app/Http/Controllers/Admin/AdminQrController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableQrCode;
use App\Services\Admin\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AdminQrController extends Controller
{
    public function __construct(
        private QrCodeService $qrCodeService
    ) {}

    /**
     * Download single QR code as PNG
     */
    public function download(TableQrCode $qrCode): Response
    {
        $filePath = $this->qrCodeService->downloadQrCode($qrCode);
        $fileName = "table-{$qrCode->table->table_number}-qr-code.png";
        
        return response()->download($filePath, $fileName);
    }

    /**
     * Download QR code as PDF with branding
     */
    public function downloadPdf(TableQrCode $qrCode): Response
    {
        $filePath = $this->qrCodeService->downloadQrCodePdf($qrCode);
        $fileName = "table-{$qrCode->table->table_number}-qr-code.pdf";
        
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Download QR code with table number overlay
     */
    public function downloadWithTableNumber(TableQrCode $qrCode): Response
    {
        $filePath = $this->qrCodeService->downloadQrCodeWithTableNumber($qrCode);
        $fileName = "table-{$qrCode->table->table_number}-qr-code-with-number.png";
        
        return response()->download($filePath, $fileName);
    }

    /**
     * Download multiple QR codes as ZIP
     */
    public function bulkDownloadZip(Request $request): Response
    {
        $qrCodeIds = $request->input('qr_codes', []);
        $qrCodes = TableQrCode::whereIn('id', $qrCodeIds)->get();
        
        $filePath = $this->qrCodeService->downloadBulkQrCodes($qrCodes->toArray());
        $fileName = 'qr-codes-' . now()->format('Y-m-d-H-i-s') . '.zip';
        
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Download all active QR codes as ZIP
     */
    public function bulkDownloadAll(): Response
    {
        $qrCodes = TableQrCode::where('is_active', true)->get();
        
        $filePath = $this->qrCodeService->downloadBulkQrCodes($qrCodes->toArray());
        $fileName = 'all-qr-codes-' . now()->format('Y-m-d-H-i-s') . '.zip';
        
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}
```

### QR Code PDF Template
```blade
{{-- resources/views/admin/qr-codes/pdf-template.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code - Table {{ $table->table_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 20px;
        }
        .store-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .table-info {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .qr-code {
            margin: 20px 0;
        }
        .qr-code img {
            width: 300px;
            height: 300px;
        }
        .table-number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-top: 15px;
            padding: 10px 20px;
            border: 2px solid #333;
            border-radius: 8px;
            display: inline-block;
            background-color: #f8f9fa;
        }
        .instructions {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
            line-height: 1.5;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="store-name">{{ $store->name }}</div>
        <div class="table-info">Table {{ $table->table_number }}</div>
    </div>
    
    <div class="qr-code">
        <img src="{{ $qrCodePath }}" alt="QR Code for Table {{ $table->table_number }}">
        <div class="table-number">Table {{ $table->table_number }}</div>
    </div>
    
    <div class="instructions">
        <p><strong>Scan this QR code to order from your table</strong></p>
        <p>Point your camera at the QR code above to access our menu and place your order.</p>
    </div>
    
    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y g:i A') }}</p>
        <p>{{ $store->name }} - {{ $store->address }}</p>
    </div>
</body>
</html>
```

### Livewire Download Component
```php
<?php
// app/Livewire/Admin/QrCodes/QrDownloader.php

namespace App\Livewire\Admin\QrCodes;

use App\Models\TableQrCode;
use App\Services\Admin\QrCodeService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class QrDownloader extends Component
{
    public $selectedQrCodes = [];
    public $downloadFormat = 'png';
    public $includeBranding = true;

    public function downloadSingle(TableQrCode $qrCode)
    {
        if ($this->downloadFormat === 'pdf') {
            return redirect()->route('admin.qr-codes.download-pdf', $qrCode);
        } elseif ($this->downloadFormat === 'with-number') {
            return redirect()->route('admin.qr-codes.download-with-number', $qrCode);
        }
        
        return redirect()->route('admin.qr-codes.download', $qrCode);
    }

    public function downloadBulk()
    {
        if (empty($this->selectedQrCodes)) {
            $this->dispatch('flash', type: 'error', message: 'Please select QR codes to download.');
            return;
        }

        if ($this->downloadFormat === 'zip') {
            return redirect()->route('admin.qr-codes.bulk-download-zip')
                ->with('qr_codes', $this->selectedQrCodes);
        }
        
        // Handle other bulk formats
        $this->dispatch('flash', type: 'info', message: 'Bulk download started...');
    }

    public function downloadAll()
    {
        return redirect()->route('admin.qr-codes.bulk-download-all');
    }

    public function render()
    {
        $qrCodes = TableQrCode::with(['table', 'table.store'])
            ->where('is_active', true)
            ->orderBy('table_id')
            ->get();

        return view('livewire.admin.qr-codes.qr-downloader', [
            'qrCodes' => $qrCodes
        ]);
    }
}
```

### Download Component View
```blade
{{-- resources/views/livewire/admin/qr-codes/qr-downloader.blade.php --}}
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Download QR Codes</h1>
        <div class="flex items-center space-x-4">
            <button wire:click="downloadAll" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download All
            </button>
        </div>
    </div>

    <!-- Download Options -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Download Options</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                <select wire:model="downloadFormat" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="png">PNG Image</option>
                    <option value="with-number">PNG with Table Number</option>
                    <option value="pdf">PDF Document</option>
                    <option value="zip">ZIP Archive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Branding</label>
                <select wire:model="includeBranding" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="true">Include Store Branding</option>
                    <option value="false">QR Code Only</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="downloadBulk" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Selected
                </button>
            </div>
        </div>
    </div>

    <!-- QR Codes List -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Available QR Codes</h3>
            <div class="space-y-3">
                @foreach($qrCodes as $qrCode)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center space-x-4">
                            <input type="checkbox" 
                                   wire:model="selectedQrCodes" 
                                   value="{{ $qrCode->id }}"
                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    Table {{ $qrCode->table->table_number }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $qrCode->table->store->name }} - {{ $qrCode->table->location_description ?? 'Main Area' }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button wire:click="downloadSingle({{ $qrCode->id }})" 
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
```

## Admin Interface Updates

### Updated Orders Table Structure
```blade
<!-- Add Table column after Customer -->
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>

<!-- Add Order Type column after Status -->
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>

<!-- In table body -->
<td class="px-6 py-4 whitespace-nowrap">
    @if($order->table_id)
        <div class="text-sm font-medium text-gray-900">Table {{ $order->table_number }}</div>
        <div class="text-xs text-gray-500">{{ $order->table->location_description ?? 'Main Area' }}</div>
    @else
        <span class="text-gray-400">-</span>
    @endif
</td>

<td class="px-6 py-4 whitespace-nowrap">
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->order_type_color_class }}">
        {{ $order->order_type_display }}
    </span>
</td>
```

### Kitchen Display Updates
```blade
<!-- Group orders by table -->
@foreach($orders->groupBy('table_id') as $tableId => $tableOrders)
    <div class="mb-6 p-4 bg-white border border-gray-200 rounded-lg">
        @if($tableId)
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Table {{ $tableOrders->first()->table_number }}
                </h3>
                <span class="text-sm text-gray-500">
                    {{ $tableOrders->count() }} order(s)
                </span>
            </div>
        @else
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Delivery/Pickup Orders
            </h3>
        @endif
        
        <!-- Orders for this table -->
        @foreach($tableOrders as $order)
            <!-- Order details -->
        @endforeach
    </div>
@endforeach
```

## Conclusion

This table QR system will transform the restaurant ordering experience by enabling seamless table-specific ordering through QR code scanning. The phased implementation approach ensures minimal disruption to existing operations while providing a robust foundation for future enhancements.

The system integrates seamlessly with the existing Laravel/Livewire architecture and follows established patterns for consistency and maintainability. The `simplesoftwareio/simple-qrcode` package provides reliable QR code generation with logo embedding capabilities, making it perfect for restaurant branding.

**Key Benefits**:
- **Enhanced Order Management**: Clear table context for staff
- **Improved Kitchen Operations**: Group orders by table for efficient service
- **Better Customer Experience**: Seamless table-specific ordering
- **Operational Efficiency**: Streamlined table management and QR code generation

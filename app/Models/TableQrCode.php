<?php

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

    /**
     * Check if QR code is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if QR code is valid (active and not expired)
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Generate unique QR code identifier
     */
    public function generateQrCode(): string
    {
        return 'TBL_' . $this->table_id . '_' . uniqid();
    }

    /**
     * Generate QR code URL
     */
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
        $fileName = "qr/table-{$this->table_id}-{$this->qr_code}.png";

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
        $fileName = "qr/table-{$this->table_id}-{$this->qr_code}.png";

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
        $fileName = "qr/table-{$this->table_id}-{$this->qr_code}-logo.png";

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
        $fileName = "qr/table-{$this->table_id}-{$this->qr_code}-with-number.png";

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

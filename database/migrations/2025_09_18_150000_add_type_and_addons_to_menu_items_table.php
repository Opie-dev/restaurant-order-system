<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('type')->nullable()->after('stock'); // 'set' | 'ala_carte'
            $table->decimal('base_price', 10, 2)->nullable()->after('price');
            $table->json('options')->nullable()->after('image_path');
            $table->json('addons')->nullable()->after('options');
            $table->boolean('enabled')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['type', 'base_price', 'options', 'addons', 'enabled']);
        });
    }
};

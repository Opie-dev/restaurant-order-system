<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            if (!Schema::hasColumn('carts', 'store_id')) {
                $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
                $table->index(['store_id', 'user_id']);
                $table->index(['store_id', 'guest_token']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            if (Schema::hasColumn('carts', 'store_id')) {
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            }
        });
    }
};

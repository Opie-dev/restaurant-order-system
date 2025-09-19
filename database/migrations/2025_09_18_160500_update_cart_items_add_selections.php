<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('cart_items', 'selections')) {
            Schema::table('cart_items', function (Blueprint $table): void {
                $table->json('selections')->nullable()->after('unit_price');
            });
        }

        // Drop unique constraint to allow multiple lines for same item with different selections
        // Need to temporarily drop foreign keys to remove the composite index on (cart_id, menu_item_id)
        Schema::table('cart_items', function (Blueprint $table): void {
            if (Schema::hasColumn('cart_items', 'cart_id')) {
                $table->dropForeign('cart_items_cart_id_foreign');
            }
            if (Schema::hasColumn('cart_items', 'menu_item_id')) {
                $table->dropForeign('cart_items_menu_item_id_foreign');
            }
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            // Laravel names the unique as cart_items_cart_id_menu_item_id_unique by default
            $table->dropUnique('cart_items_cart_id_menu_item_id_unique');
            $table->index(['cart_id']);
            $table->index(['menu_item_id']);
        });

        // Re-create foreign keys
        Schema::table('cart_items', function (Blueprint $table): void {
            $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table): void {
            $table->dropForeign('cart_items_cart_id_foreign');
            $table->dropForeign('cart_items_menu_item_id_foreign');
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->dropIndex(['cart_id']);
            $table->dropIndex(['menu_item_id']);
            $table->unique(['cart_id', 'menu_item_id']);
            $table->dropColumn('selections');
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete();
        });
    }
};

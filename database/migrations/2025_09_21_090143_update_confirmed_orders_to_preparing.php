<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all orders with 'confirmed' status to 'preparing' status
        // since we're removing the confirmed status from the workflow
        DB::table('orders')
            ->where('status', 'confirmed')
            ->update(['status' => 'preparing']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We cannot safely reverse this migration as we don't know
        // which orders were originally 'confirmed' vs 'preparing'
        // This is a one-way migration
    }
};

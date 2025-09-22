<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('address_id')->nullable()->after('user_id');
            $table->string('ship_recipient_name')->nullable()->after('address_id');
            $table->string('ship_phone')->nullable()->after('ship_recipient_name');
            $table->string('ship_line1')->nullable()->after('ship_phone');
            $table->string('ship_line2')->nullable()->after('ship_line1');
            $table->string('ship_city')->nullable()->after('ship_line2');
            $table->string('ship_state')->nullable()->after('ship_city');
            $table->string('ship_postal_code')->nullable()->after('ship_state');
            $table->string('ship_country', 2)->nullable()->after('ship_postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'address_id',
                'ship_recipient_name',
                'ship_phone',
                'ship_line1',
                'ship_line2',
                'ship_city',
                'ship_state',
                'ship_postal_code',
                'ship_country',
            ]);
        });
    }
};

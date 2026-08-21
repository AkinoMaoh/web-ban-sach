<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->text('shipping_address')->nullable()->change();
        });

        Schema::table('user_addresses', function (Blueprint $table): void {
            $table->string('specific_address', 500)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('shipping_address')->nullable()->change();
        });

        Schema::table('user_addresses', function (Blueprint $table): void {
            $table->string('specific_address')->change();
        });
    }
};

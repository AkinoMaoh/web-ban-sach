<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('payment_method', 100);
            $table->decimal('amount', 15, 2);
            $table->string('status', 50)->default('pending');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
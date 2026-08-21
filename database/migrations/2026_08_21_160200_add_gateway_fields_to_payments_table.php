<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->string('transaction_ref', 64)->nullable()->unique();
            $table->string('gateway_transaction_no', 100)->nullable()->unique();
            $table->string('response_code', 20)->nullable();
            $table->string('bank_code', 30)->nullable();
            $table->string('card_type', 30)->nullable();
            $table->string('currency', 3)->default('VND');
            $table->unsignedSmallInteger('attempt')->default(1);
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status'], 'payments_order_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropIndex('payments_order_status_index');
            $table->dropUnique(['transaction_ref']);
            $table->dropUnique(['gateway_transaction_no']);
            $table->dropColumn([
                'transaction_ref',
                'gateway_transaction_no',
                'response_code',
                'bank_code',
                'card_type',
                'currency',
                'attempt',
                'gateway_payload',
                'paid_at',
                'failed_at',
                'expired_at',
                'refunded_at',
                'created_at',
                'updated_at',
            ]);
        });
    }
};

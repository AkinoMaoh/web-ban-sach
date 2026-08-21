<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('name', 150)->nullable()->after('code');
            $table->text('description')->nullable()->after('name');
            $table->unsignedInteger('usage_limit_per_customer')->nullable()->after('usage_limit');
            $table->boolean('is_public')->default(true)->after('is_active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['is_active', 'is_public', 'start_date', 'end_date'],
                'vouchers_availability_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropIndex('vouchers_availability_index');
            $table->dropColumn([
                'name',
                'description',
                'usage_limit_per_customer',
                'is_public',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);
        });
    }
};

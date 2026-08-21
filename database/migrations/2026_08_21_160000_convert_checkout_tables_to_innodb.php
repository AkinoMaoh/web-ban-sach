<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $tables = [
            'users',
            'products',
            'product_variants',
            'carts',
            'vouchers',
            'voucher_usages',
            'orders',
            'order_details',
            'payments',
            'notifications',
            'user_addresses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` ENGINE=InnoDB");
            }
        }
    }

    public function down(): void
    {
        // Khong chuyen nguoc ve MyISAM vi se lam mat ho tro transaction va row lock.
    }
};

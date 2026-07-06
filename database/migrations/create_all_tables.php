
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng authors
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('status');
            $table->timestamps();
        });

        // Bảng categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Bảng comments
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('product_id');
            $table->tinyInteger('rating')->default(5);
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();
        });

        // Bảng orders
            Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable(); // Cho phép null khi khách mua không cần tài khoản
            $table->string('billing_email')->nullable(); // Thêm cột lưu email để gửi hóa đơn
            $table->integer('voucher_id')->nullable();
            $table->integer('discount_id')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->string('status', 50)->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->string('shipping_name')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_method')->nullable();
        });

        // Bảng order_details
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('product_variant_id');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
        });

        // Bảng payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('payment_method', 100);
            $table->decimal('amount', 15, 2);
            $table->string('status', 50)->default('pending');
        });

        // Bảng products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->integer('author_id');
            $table->integer('publisher_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Bảng product_variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->enum('edition', [
                'Standard',
                'Special',
                'Special Signed'
            ])->default('Standard');
            $table->string('sku', 100)->unique()->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        // Bảng publishers
        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Bảng roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
        });

        // Bảng users
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('phone', 20)->nullable();
                $table->text('address')->nullable();
                $table->string('avatar')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->integer('role');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // Bảng vouchers
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->decimal('discount_value', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
        });

        // Bảng carts
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_variant_id');
            $table->integer('quantity')->default(1);
            $table->timestamps(); 
        });

        // Bảng wishlists
        Schema::create('wishlists', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('product_id'); // Lưu theo ID của Sản phẩm (Sách)
        $table->timestamps();
    });
    }

    public function down(): void
    {
        $tables = [
            'vouchers',
            'users',
            'roles',
            'publishers',
            'product_variants',
            'products',
            'payments',
            'order_details',
            'orders',
            'comments',
            'categories',
            'authors',
            'wishlists',
            'carts',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};


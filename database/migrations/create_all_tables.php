<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng attributes
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        // Bảng attribute_values
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->integer('attribute_id');
            $table->integer('product_variant_id');
            $table->string('value');
        });

        // Bảng authors
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('status');
            $table->timestamps();
        });

        // Bảng book_authors
        Schema::create('book_authors', function (Blueprint $table) {
            $table->integer('product_id');
            $table->integer('author_id');
            $table->primary(['product_id', 'author_id']);
        });

        // Bảng brands
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        // Bảng carts
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
        });

        // Bảng cart_items
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->integer('cart_id');
            $table->integer('product_variant_id');
            $table->integer('quantity')->default(1);
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

        // Bảng discounts
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('discount_percent', 5, 2);
            $table->date('start_date');
            $table->date('end_date');
        });

        // Bảng genres
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_id')->nullable();
        });

        // Bảng news
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->string('title');
            $table->string('image')->nullable();
            $table->text('content')->nullable();
        });

        // Bảng news_categories
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        // Bảng orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
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
            $table->string('product_method', 100);
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
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Bảng product_categories
        Schema::create('product_categories', function (Blueprint $table) {
            $table->integer('product_id');
            $table->integer('category_id');
            $table->primary(['product_id', 'category_id']);
        });

        // Bảng product_genres
        Schema::create('product_genres', function (Blueprint $table) {
            $table->integer('product_id');
            $table->integer('genre_id');
            $table->primary(['product_id', 'genre_id']);
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
            $table->string('sku', 100)
                ->unique()
                ->nullable();
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

        // Bảng replies
        Schema::create('replies', function (Blueprint $table) {
            $table->id();
            $table->integer('comment_id');
            $table->integer('admin_id');
            $table->text('content');
        });

        // Bảng reveneus
        Schema::create('reveneus', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->decimal('amount', 15, 2);
            $table->date('date');
        });

        // Bảng roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
        });

        // Bảng users (Ghi đè hoặc tuỳ biến theo DB cũ)
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
                $table->string('remember_token', 100)->nullable();
                $table->timestamps();
            });
        }

        // Bảng user_roles
        Schema::create('user_roles', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('role_id');
            $table->primary(['user_id', 'role_id']);
        });

        // Bảng vouchers
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->decimal('discount_value', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
        });

        // Bảng wishlists
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('product_id');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['user_id', 'product_id'], 'uq_wishlist');
        });
    }

    public function down(): void
    {
        $tables = [
            'wishlists',
            'vouchers',
            'user_roles',
            'users',
            'roles',
            'reveneus',
            'replies',
            'publishers',
            'product_variants',
            'product_genres',
            'product_categories',
            'products',
            'payments',
            'order_details',
            'orders',
            'news_categories',
            'news',
            'genres',
            'discounts',
            'comments',
            'categories',
            'cart_items',
            'carts',
            'brands',
            'book_authors',
            'authors',
            'attribute_values',
            'attributes'
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};

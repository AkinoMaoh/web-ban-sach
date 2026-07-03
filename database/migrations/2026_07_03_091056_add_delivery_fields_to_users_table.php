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
        Schema::table('users', function (Blueprint $table) {
            // Nếu CHƯA CÓ cột phone thì mới thêm
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            // Nếu CHƯA CÓ cột gender thì mới thêm
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('phone');
            }
            
            // Nếu CHƯA CÓ cột address thì mới thêm
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('gender');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Khi xóa thì check xem cột nào có thì mới xóa
            $columns = [];
            if (Schema::hasColumn('users', 'phone')) $columns[] = 'phone';
            if (Schema::hasColumn('users', 'gender')) $columns[] = 'gender';
            if (Schema::hasColumn('users', 'address')) $columns[] = 'address';
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
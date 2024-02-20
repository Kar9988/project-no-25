<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('likes', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('role', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('user_plans', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('likes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('role', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('user_plans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

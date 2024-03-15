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
            $table->string('name')->nullable()->change();
            $table->float('price')->nullable()->change();
            $table->unsignedBigInteger('points')->nullable()->change();
            $table->longText('description')->nullable()->change();
            $table->unsignedBigInteger('discount')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
           $table->string('name')->change();
            $table->unsignedBigInteger('price')->change();
            $table->unsignedBigInteger('points')->change();
            $table->longText('description')->change();
            $table->unsignedBigInteger('discount')->change();
        });
    }
};

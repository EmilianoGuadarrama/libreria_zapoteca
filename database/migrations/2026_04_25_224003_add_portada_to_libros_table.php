<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('libros', 'portada')) {
            Schema::table('libros', function (Blueprint $table) {
                $table->string('portada', 255)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('libros', 'portada')) {
            Schema::table('libros', function (Blueprint $table) {
                $table->dropColumn('portada');
            });
        }
    }
};
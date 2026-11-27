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
        foreach (['materi', 'pengumpulan'] as $namaTabel) {
            Schema::table($namaTabel, function (Blueprint $table) {
                $table->json('lampiran')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['materi', 'pengumpulan'] as $namaTabel) {
            Schema::table($namaTabel, function (Blueprint $table) {
                $table->dropColumn('lampiran');
            });
        }
    }
};

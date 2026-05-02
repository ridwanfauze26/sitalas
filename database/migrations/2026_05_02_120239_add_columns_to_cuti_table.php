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
        Schema::table('cuti', function (Blueprint $table) {
            $table->string('nama',100)->nullable()->after('user_id');
            $table->string('jabatan',100)->nullable()->after('nama');
            $table->string('unit_kerja',50)->nullable()->after('jabatan');
            $table->string('masa_kerja',30)->nullable()->after('unit_kerja');
            $table->string('atasan',100)->nullable()->after('unit_kerja');
            $table->string('pejabat',100)->nullable()->after('unit_kerja');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropColumn(['nama', 'jabatan', 'unit_kerja','masa_kerja','atasan','pejabat']);
        });
    }
};

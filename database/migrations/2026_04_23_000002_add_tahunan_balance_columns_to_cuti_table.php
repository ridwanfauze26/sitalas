<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cuti')) {
            return;
        }

        Schema::table('cuti', function (Blueprint $table) {
            if (!Schema::hasColumn('cuti', 'tahun_cuti')) {
                $table->unsignedSmallInteger('tahun_cuti')->nullable()->after('level_pengaju');
            }
            if (!Schema::hasColumn('cuti', 'lama_cuti_hari_kerja')) {
                $table->unsignedTinyInteger('lama_cuti_hari_kerja')->nullable()->after('lama_cuti');
            }
            if (!Schema::hasColumn('cuti', 'potong_n')) {
                $table->unsignedTinyInteger('potong_n')->nullable()->after('lama_cuti_hari_kerja');
            }
            if (!Schema::hasColumn('cuti', 'potong_n1')) {
                $table->unsignedTinyInteger('potong_n1')->nullable()->after('potong_n');
            }
            if (!Schema::hasColumn('cuti', 'potong_n2')) {
                $table->unsignedTinyInteger('potong_n2')->nullable()->after('potong_n1');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('cuti')) {
            return;
        }

        Schema::table('cuti', function (Blueprint $table) {
            foreach (['potong_n2', 'potong_n1', 'potong_n', 'lama_cuti_hari_kerja', 'tahun_cuti'] as $col) {
                if (Schema::hasColumn('cuti', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jabatan')) {
            return;
        }

        Schema::table('jabatan', function (Blueprint $table) {
            if (!Schema::hasColumn('jabatan', 'level')) {
                $table->unsignedTinyInteger('level')->nullable();
            }
        });

        if (Schema::hasColumn('jabatan', 'level')) {
            DB::table('jabatan')->where('nama', 'Kepala Balai')->update(['level' => 1]);
            DB::table('jabatan')->whereIn('nama', [
                'Kepala Sub Bagian Tata Usaha',
                'Sub Koordinator Substansi Penyiapan Sampel',
                'Sub Koordinator Substansi Pelayanan Teknik',
            ])->update(['level' => 2]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('jabatan')) {
            return;
        }

        Schema::table('jabatan', function (Blueprint $table) {
            if (Schema::hasColumn('jabatan', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};

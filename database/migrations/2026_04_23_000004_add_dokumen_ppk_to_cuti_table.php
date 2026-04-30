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
            if (!Schema::hasColumn('cuti', 'dokumen_ppk')) {
                $table->string('dokumen_ppk')->nullable()->after('dokumen_sakit');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('cuti')) {
            return;
        }

        Schema::table('cuti', function (Blueprint $table) {
            if (Schema::hasColumn('cuti', 'dokumen_ppk')) {
                $table->dropColumn('dokumen_ppk');
            }
        });
    }
};

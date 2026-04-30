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
            if (!Schema::hasColumn('cuti', 'dokumen_sakit')) {
                $table->string('dokumen_sakit')->nullable()->after('no_telepon');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('cuti')) {
            return;
        }

        Schema::table('cuti', function (Blueprint $table) {
            if (Schema::hasColumn('cuti', 'dokumen_sakit')) {
                $table->dropColumn('dokumen_sakit');
            }
        });
    }
};

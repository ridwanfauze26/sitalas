<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalColumnsToCutiTable extends Migration
{
    public function up()
    {
        Schema::table('cuti', function (Blueprint $table) {
            if (!Schema::hasColumn('cuti', 'level_pengaju')) {
                $table->unsignedTinyInteger('level_pengaju')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('cuti', 'status_level1')) {
                $table->string('status_level1', 20)->default('Tidak Perlu')->after('status_pengajuan');
            }
            if (!Schema::hasColumn('cuti', 'status_level2')) {
                $table->string('status_level2', 20)->default('Tidak Perlu')->after('status_level1');
            }
            if (!Schema::hasColumn('cuti', 'approved_level1_by')) {
                $table->unsignedBigInteger('approved_level1_by')->nullable()->after('status_level2');
            }
            if (!Schema::hasColumn('cuti', 'approved_level1_at')) {
                $table->timestamp('approved_level1_at')->nullable()->after('approved_level1_by');
            }
            if (!Schema::hasColumn('cuti', 'approved_level2_by')) {
                $table->unsignedBigInteger('approved_level2_by')->nullable()->after('approved_level1_at');
            }
            if (!Schema::hasColumn('cuti', 'approved_level2_at')) {
                $table->timestamp('approved_level2_at')->nullable()->after('approved_level2_by');
            }
            if (!Schema::hasColumn('cuti', 'rejected_reason')) {
                $table->string('rejected_reason', 200)->nullable()->after('approved_level2_at');
            }
        });
    }

    public function down()
    {
        Schema::table('cuti', function (Blueprint $table) {
            if (Schema::hasColumn('cuti', 'rejected_reason')) {
                $table->dropColumn('rejected_reason');
            }
            if (Schema::hasColumn('cuti', 'approved_level2_at')) {
                $table->dropColumn('approved_level2_at');
            }
            if (Schema::hasColumn('cuti', 'approved_level2_by')) {
                $table->dropColumn('approved_level2_by');
            }
            if (Schema::hasColumn('cuti', 'approved_level1_at')) {
                $table->dropColumn('approved_level1_at');
            }
            if (Schema::hasColumn('cuti', 'approved_level1_by')) {
                $table->dropColumn('approved_level1_by');
            }
            if (Schema::hasColumn('cuti', 'status_level2')) {
                $table->dropColumn('status_level2');
            }
            if (Schema::hasColumn('cuti', 'status_level1')) {
                $table->dropColumn('status_level1');
            }
            if (Schema::hasColumn('cuti', 'level_pengaju')) {
                $table->dropColumn('level_pengaju');
            }
        });
    }
}

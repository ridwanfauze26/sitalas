<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJabatanRoleNipToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'jabatan_id')) {
                $table->unsignedInteger('jabatan_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 11)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'nip')) {
                $table->dropColumn('nip');
            }
            if (Schema::hasColumn('users', 'jabatan_id')) {
                $table->dropColumn('jabatan_id');
            }
        });
    }
}

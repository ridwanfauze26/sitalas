<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJabatanTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('jabatan')) {
            return;
        }

        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
        });
    }

    public function down()
    {
        Schema::dropIfExists('jabatan');
    }
}

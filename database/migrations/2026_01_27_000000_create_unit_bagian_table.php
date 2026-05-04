<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitBagianTable extends Migration
{
    public function up()
    {
        Schema::create('unit_bagian', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 100);
            $table->foreignId('jabatan_id')->constrained('jabatan')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('unit_bagian');
    }
}

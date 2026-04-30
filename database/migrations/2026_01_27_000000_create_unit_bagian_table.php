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
        });
    }

    public function down()
    {
        Schema::dropIfExists('unit_bagian');
    }
}

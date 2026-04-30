<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cuti_tahunan_balances')) {
            return;
        }

        Schema::create('cuti_tahunan_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('jatah')->default(12);
            $table->unsignedTinyInteger('dipakai')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'tahun']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti_tahunan_balances');
    }
};

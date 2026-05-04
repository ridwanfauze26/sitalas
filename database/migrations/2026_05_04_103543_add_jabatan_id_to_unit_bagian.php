<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    //menambahkan kolom dan foreign key sebelumnya harus mengosongkan data di tabel unit_bagian
    //kemudian mengubah tipe data id pada tabel jabatan 
    public function up(): void
    {
        Schema::table('unit_bagian', function (Blueprint $table) {
             $table->foreignId('jabatan_id')
                  ->constrained('jabatan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_bagian', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);

            $table->dropColumn('jabatan_id');
        });
    }
};

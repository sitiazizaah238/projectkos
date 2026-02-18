<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::create('kos', function (Blueprint $table) {
        $table->id();

        // pemilik kos
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        $table->string('nama_kos');
        $table->string('lokasi');
        $table->string('tipe_kos');
        $table->text('deskripsi')->nullable();
        $table->string('foto')->nullable();

        // status pengajuan ke admin
        $table->enum('status', ['menunggu','disetujui','ditolak'])
              ->default('menunggu');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kos');
    }
};

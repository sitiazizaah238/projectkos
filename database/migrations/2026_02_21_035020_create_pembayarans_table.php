<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('pembayarans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pengajuan_sewa_id')->constrained()->onDelete('cascade');
        $table->foreignId('metode_id')->constrained('metode_pembayarans')->onDelete('cascade');
        $table->string('bukti');
        $table->enum('status',['menunggu','dikonfirmasi','ditolak'])->default('menunggu');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};

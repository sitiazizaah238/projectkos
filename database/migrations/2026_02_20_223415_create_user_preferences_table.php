<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Nilai tengah dari range filter [cite: 25]
            $table->integer('pref_harga')->nullable();

            // Putra/Putri/Campur [cite: 26]
            $table->string('pref_tipe_kos')->nullable();

            // Array fasilitas yang diinginkan [cite: 27]
            $table->json('pref_fasilitas')->nullable();

            // Bulanan/Tahunan [cite: 28]
            $table->string('pref_tipe_harga')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};

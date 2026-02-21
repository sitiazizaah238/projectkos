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
    Schema::table('pengajuan_sewas', function (Blueprint $table) {
        $table->bigInteger('total_bayar')->nullable();
        $table->text('alasan')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_sewas', function (Blueprint $table) {
            //
        });
    }
};

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
    if (!Schema::hasColumn('kos', 'is_read')) {
        Schema::table('kos', function (Blueprint $table) {
            $table->boolean('is_read')->default(false);
        });
    }

    if (!Schema::hasColumn('pengajuan_sewas', 'is_read')) {
        Schema::table('pengajuan_sewas', function (Blueprint $table) {
            $table->boolean('is_read')->default(false);
        });
    }
}
    /**
     * Reverse the migrations.
     */
  public function down(): void
{
    if (Schema::hasColumn('kos', 'is_read')) {
        Schema::table('kos', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }

    if (Schema::hasColumn('pengajuan_sewas', 'is_read')) {
        Schema::table('pengajuan_sewas', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
}
};

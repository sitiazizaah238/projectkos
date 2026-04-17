<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pengajuan_sewas', function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_sewas', 'jenis_pengajuan')) {
                $table->string('jenis_pengajuan', 20)->default('sewa_baru')->after('status');
            }
        });

        // Backfill data lama agar tetap konsisten.
        DB::table('pengajuan_sewas')
            ->whereNull('jenis_pengajuan')
            ->update(['jenis_pengajuan' => 'sewa_baru']);
    }

    public function down(): void
    {
        Schema::table('pengajuan_sewas', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_sewas', 'jenis_pengajuan')) {
                $table->dropColumn('jenis_pengajuan');
            }
        });
    }
};

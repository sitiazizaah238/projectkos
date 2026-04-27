<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayarans', 'durasi_tagihan')) {
                $table->unsignedInteger('durasi_tagihan')->default(1)->after('bukti');
            }

            if (!Schema::hasColumn('pembayarans', 'nominal_tagihan')) {
                $table->unsignedBigInteger('nominal_tagihan')->default(0)->after('durasi_tagihan');
            }
        });

        if (!Schema::hasTable('pengajuan_sewas')) {
            return;
        }

        $hasDurasiColumn = Schema::hasColumn('pengajuan_sewas', 'durasi');
        $hasTotalBayarColumn = Schema::hasColumn('pengajuan_sewas', 'total_bayar');

        DB::table('pembayarans')
            ->where(function ($query) {
                $query->whereNull('nominal_tagihan')->orWhere('nominal_tagihan', 0);
            })
            ->update([
                'durasi_tagihan' => DB::raw(
                    $hasDurasiColumn
                        ? 'COALESCE(NULLIF((SELECT ps.durasi FROM pengajuan_sewas ps WHERE ps.id = pembayarans.pengajuan_sewa_id), 0), 1)'
                        : '1'
                ),
                'nominal_tagihan' => DB::raw(
                    $hasTotalBayarColumn
                        ? 'COALESCE((SELECT ps.total_bayar FROM pengajuan_sewas ps WHERE ps.id = pembayarans.pengajuan_sewa_id), 0)'
                        : '0'
                ),
            ]);
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (Schema::hasColumn('pembayarans', 'nominal_tagihan')) {
                $table->dropColumn('nominal_tagihan');
            }

            if (Schema::hasColumn('pembayarans', 'durasi_tagihan')) {
                $table->dropColumn('durasi_tagihan');
            }
        });
    }
};

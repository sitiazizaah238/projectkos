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

        DB::table('pembayarans as p')
            ->join('pengajuan_sewas as ps', 'ps.id', '=', 'p.pengajuan_sewa_id')
            ->where(function ($query) {
                $query->whereNull('p.nominal_tagihan')->orWhere('p.nominal_tagihan', 0);
            })
            ->update([
                'p.durasi_tagihan' => DB::raw('COALESCE(NULLIF(ps.durasi, 0), 1)'),
                'p.nominal_tagihan' => DB::raw('COALESCE(ps.total_bayar, 0)'),
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

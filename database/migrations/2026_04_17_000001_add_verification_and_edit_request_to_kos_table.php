<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('kos')) {
            $driver = DB::getDriverName();

            if (!Schema::hasColumn('kos', 'tanggal_verifikasi')) {
                Schema::table('kos', function (Blueprint $table) {
                    $table->timestamp('tanggal_verifikasi')->nullable()->after('status');
                });
            }

            if (!Schema::hasColumn('kos', 'edit_request_status')) {
                Schema::table('kos', function (Blueprint $table) {
                    $table->string('edit_request_status', 20)->default('tidak_ada')->after('tanggal_verifikasi');
                });
            }

            if (!Schema::hasColumn('kos', 'edit_request_data')) {
                Schema::table('kos', function (Blueprint $table) {
                    $table->json('edit_request_data')->nullable()->after('edit_request_status');
                });
            }

            if (!Schema::hasColumn('kos', 'edit_request_alasan')) {
                Schema::table('kos', function (Blueprint $table) {
                    $table->text('edit_request_alasan')->nullable()->after('edit_request_data');
                });
            }

            if (!Schema::hasColumn('kos', 'edit_requested_at')) {
                Schema::table('kos', function (Blueprint $table) {
                    $table->timestamp('edit_requested_at')->nullable()->after('edit_request_alasan');
                });
            }

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement("ALTER TABLE kos MODIFY COLUMN status ENUM('menunggu','disetujui','ditolak','nonaktif') DEFAULT 'menunggu'");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kos')) {
            $driver = DB::getDriverName();

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement("ALTER TABLE kos MODIFY COLUMN status ENUM('menunggu','disetujui','ditolak') DEFAULT 'menunggu'");
            }

            $columns = [
                'tanggal_verifikasi',
                'edit_request_status',
                'edit_request_data',
                'edit_request_alasan',
                'edit_requested_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('kos', $column)) {
                    Schema::table('kos', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }
    }
};

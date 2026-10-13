<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom penanda tangan & identitas Berita Acara per sesi evaluasi unit.
     */
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->string('ba_nomor')->nullable()->after('evaluator_notes');
            $table->string('ba_tempat')->nullable()->after('ba_nomor');
            $table->date('ba_tanggal')->nullable()->after('ba_tempat');

            $table->string('pejabat_nama')->nullable()->after('ba_tanggal');
            $table->string('pejabat_jabatan')->nullable()->after('pejabat_nama');
            $table->string('pejabat_nip')->nullable()->after('pejabat_jabatan');

            $table->string('penilai_nama')->nullable()->after('pejabat_nip');
            $table->string('penilai_jabatan')->nullable()->after('penilai_nama');
            $table->string('penilai_nip')->nullable()->after('penilai_jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'ba_nomor', 'ba_tempat', 'ba_tanggal',
                'pejabat_nama', 'pejabat_jabatan', 'pejabat_nip',
                'penilai_nama', 'penilai_jabatan', 'penilai_nip',
            ]);
        });
    }
};

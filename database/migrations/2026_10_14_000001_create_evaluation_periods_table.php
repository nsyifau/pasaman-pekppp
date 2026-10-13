<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Periode penilaian tahunan (buka/tutup/kunci) beserta jejak auditnya.
     */
    public function up(): void
    {
        Schema::create('evaluation_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unique();
            $table->enum('status', ['open', 'closed', 'locked'])->default('closed');
            $table->text('notes')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('evaluation_period_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_period_id')->constrained('evaluation_periods')->cascadeOnDelete();
            $table->enum('action', ['open', 'close', 'lock', 'reopen']);
            $table->text('reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Backfill: satu periode untuk tiap tahun evaluasi yang sudah ada.
        $years = DB::table('evaluations')->distinct()->orderBy('year')->pluck('year');

        // Jika belum ada data evaluasi, tetap sediakan periode tahun berjalan.
        if ($years->isEmpty()) {
            $years = collect([(int) date('Y')]);
        }

        $now = now();
        $latest = (int) $years->max();

        foreach ($years as $year) {
            DB::table('evaluation_periods')->insert([
                'year' => (int) $year,
                'status' => (int) $year === $latest ? 'open' : 'closed',
                'opened_at' => (int) $year === $latest ? $now : null,
                'closed_at' => (int) $year === $latest ? null : $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_period_logs');
        Schema::dropIfExists('evaluation_periods');
    }
};

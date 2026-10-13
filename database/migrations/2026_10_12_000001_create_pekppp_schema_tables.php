<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles: admin, evaluator, opd
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('role')->default('opd')->after('email'); // admin, evaluator, opd
            $table->unsignedBigInteger('opd_id')->nullable()->after('role');
            $table->string('phone')->nullable()->after('opd_id');
            $table->string('nip')->nullable()->after('phone');
        });

        // 2. Units / OPDs (Kategori: opd, puskesmas, kecamatan, nagari)
        Schema::create('opds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['opd', 'puskesmas', 'kecamatan', 'nagari'])->default('opd');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Aspek Evaluasi (6 Aspek inti + Pertanyaan Tambahan)
        Schema::create('aspects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Kebijakan Pelayanan, Profesionalisme SDM, dll
            $table->string('code')->unique(); // kebijakan_pelayanan, profesionalisme_sdm, dll
            $table->decimal('weight', 5, 2)->default(0.00); // 24.00, 25.00, 18.00, 11.00, 10.00, 12.00, 0.00
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        // 4. Indikator F-02 (30 Indikator Inti + Pertanyaan Tambahan Antrian)
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspect_id')->constrained('aspects')->cascadeOnDelete();
            $table->string('code'); // 1.a.Ak, 2.a.P, ..., 30.f.B, antrian
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('number')->default(1);
            $table->json('rubric_scales')->nullable(); // Skala 0 - 5 deskripsi
            $table->timestamps();
        });

        // 5. Pertanyaan F-01 (90 butir pertanyaan/jawaban pada sub-indikator)
        Schema::create('f01_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained('indicators')->cascadeOnDelete();
            $table->string('question_code'); // misal: 1.a, 1.b, 2.a, etc.
            $table->text('question_text');
            $table->enum('question_type', ['boolean', 'radio', 'checkbox', 'text', 'number'])->default('radio');
            $table->json('options')->nullable(); // Array of choices
            $table->text('help_text')->nullable();
            $table->text('evidence_hint')->nullable(); // Keterangan bukti dukung yang diminta
            $table->boolean('requires_drive_link')->default(true); // Kolom link google drive
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        // 6. Sesi Periode Evaluasi / Penilaian (Tahun, Unit OPD, Status)
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_id')->constrained('opds')->cascadeOnDelete();
            $table->integer('year')->default(2026);
            $table->enum('status', ['draft', 'submitted', 'in_review', 'verified', 'completed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();

            // Kolom Skor & Kalkulasi
            $table->decimal('score_f02', 8, 4)->default(0.0000); // Rata-rata/terbobot skala 0 - 5
            $table->decimal('score_f03_manual', 5, 2)->nullable(); // Nilai F-03 manual per unit, skala 1 - 100
            $table->decimal('score_f03', 8, 4)->default(0.0000); // Hasil konversi F-03 ke skala 0 - 5
            $table->decimal('final_score_ipp', 8, 4)->default(0.0000); // (F02 * 75%) + (F03 * 25%)
            $table->string('category_ipp', 5)->nullable(); // A, A-, B, B-, C, C-, D, E, F
            $table->string('category_ipp_label')->nullable(); // Pelayanan Prima, Sangat Baik, dll
            $table->text('evaluator_notes')->nullable();
            $table->timestamps();
        });

        // 7. Jawaban F-01 OPD (Submisi mandiri + Link Google Drive)
        Schema::create('f01_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('f01_question_id')->constrained('f01_questions')->cascadeOnDelete();
            $table->json('answer_value')->nullable(); // Jawaban teks/pilihan/array checkbox
            $table->text('drive_link')->nullable(); // Link Google Drive Bukti Dukung
            $table->text('opd_note')->nullable();
            $table->timestamps();
        });

        // 8. Penilaian F-02 oleh Penilai (Skor 0-5 per butir pertanyaan F-01)
        Schema::create('f02_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('f01_question_id')->constrained('f01_questions')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->default(0); // 0, 1, 2, 3, 4, 5
            $table->text('notes')->nullable(); // Catatan perbaikan dari penilai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('f02_scores');
        Schema::dropIfExists('f01_answers');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('f01_questions');
        Schema::dropIfExists('indicators');
        Schema::dropIfExists('aspects');
        Schema::dropIfExists('opds');
    }
};

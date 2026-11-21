<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evidence_submissions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->foreignId('follow_up_id')->nullable()->constrained()->nullOnDelete();
            $t->string('metric_key', 80);
            $t->string('subject_key', 80)->default('');
            $t->string('observed_value', 80);
            $t->string('unit', 30)->nullable();
            $t->dateTime('observed_at');
            $t->string('source_system', 120);   // system the observation came from
            $t->text('source_note')->nullable(); // how it was obtained (screenshot, log, ticket…)
            $t->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $t->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->text('review_note')->nullable();
            $t->dateTime('reviewed_at')->nullable();
            $t->foreignId('import_batch_id')->nullable()->constrained('data_import_batches')->nullOnDelete();
            $t->timestamps();
            $t->index(['status', 'created_at']);
            $t->index(['project_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('evidence_submissions'); }
};

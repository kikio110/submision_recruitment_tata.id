<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->string('rule_key', 60);
            $t->string('subject_key', 80)->default('');
            $t->string('fingerprint', 40);
            $t->string('category', 40);
            $t->enum('severity', ['CRITICAL', 'HIGH', 'MEDIUM', 'LOW', 'INFO']);
            $t->string('title');
            $t->text('description');
            $t->text('relevant_metrics')->nullable(); // comma list of metric keys used to verify
            $t->dateTime('baseline_at')->nullable();  // newest supporting evidence at detection
            $t->dateTime('detected_at');
            $t->dateTime('last_detected_at');
            $t->boolean('is_active')->default(true);
            $t->enum('verification_status', ['OPEN', 'PENDING_VERIFICATION', 'VERIFIED', 'UNVERIFIED', 'REOPENED'])->default('OPEN');
            $t->foreignId('source_batch_id')->nullable()->constrained('data_import_batches')->nullOnDelete();
            $t->timestamps();
            $t->unique(['project_id', 'fingerprint']);
            $t->index(['project_id', 'is_active', 'severity']);
        });

        Schema::create('finding_evidence', function (Blueprint $t) {
            $t->id();
            $t->foreignId('finding_id')->constrained()->cascadeOnDelete();
            $t->foreignId('evidence_id')->constrained('evidence_records')->cascadeOnDelete();
            $t->string('role', 20)->default('SUPPORTS'); // SUPPORTS | CONTRAST
            $t->unique(['finding_id', 'evidence_id', 'role']);
        });

        Schema::create('attention_assessments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->decimal('score', 5, 1)->nullable();
            $t->enum('level', ['HIGH', 'MEDIUM', 'LOW']);
            $t->decimal('confidence', 4, 2); // share of weight backed by data
            $t->text('criteria'); // JSON: per-criterion score/weight/status/reason
            $t->text('explanation');
            $t->dateTime('as_of');
            $t->boolean('is_current')->default(true);
            $t->foreignId('source_batch_id')->nullable()->constrained('data_import_batches')->nullOnDelete();
            $t->timestamps();
            $t->index(['project_id', 'is_current']);
        });

        Schema::create('follow_ups', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->foreignId('finding_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->text('description')->nullable();
            $t->text('instructions')->nullable();
            $t->string('assigned_pic');
            $t->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('MEDIUM');
            $t->date('due_date')->nullable();
            $t->enum('status', ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CANCELLED'])->default('OPEN');
            $t->dateTime('resolved_at')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['status', 'due_date']);
            $t->index('assigned_pic');
        });

        Schema::create('follow_up_histories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('follow_up_id')->constrained()->cascadeOnDelete();
            $t->string('event', 40); // CREATED, STATUS_CHANGED, NOTE_ADDED, REASSIGNED, UPDATED, VERIFICATION
            $t->string('from_value')->nullable();
            $t->string('to_value')->nullable();
            $t->text('note')->nullable();
            $t->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('actor_name')->nullable();
            $t->timestamp('created_at')->useCurrent(); // immutable: no updated_at
        });

        Schema::create('issue_verifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('finding_id')->constrained()->cascadeOnDelete();
            $t->foreignId('follow_up_id')->nullable()->constrained()->nullOnDelete();
            $t->enum('result', ['VERIFIED', 'UNVERIFIED', 'REOPENED', 'PENDING_VERIFICATION']);
            $t->text('explanation');
            $t->foreignId('previous_evidence_id')->nullable()->constrained('evidence_records')->nullOnDelete();
            $t->foreignId('latest_evidence_id')->nullable()->constrained('evidence_records')->nullOnDelete();
            $t->string('trigger', 30); // FOLLOW_UP_RESOLVED | MANUAL | DATA_REFRESH
            $t->dateTime('as_of');
            $t->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('created_at')->useCurrent(); // history is append-only
            $t->index(['finding_id', 'created_at']);
        });

        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->string('type', 50);
            $t->text('description');
            $t->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('actor_name')->nullable();
            $t->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('follow_up_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('batch_id')->nullable()->constrained('data_import_batches')->nullOnDelete();
            $t->timestamp('created_at')->useCurrent();
            $t->index('created_at');
            $t->index('type');
        });
    }

    public function down(): void
    {
        foreach (['activity_logs', 'issue_verifications', 'follow_up_histories', 'follow_ups', 'attention_assessments', 'finding_evidence', 'findings'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};

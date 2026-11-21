<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->enum('role', ['DIRECTOR', 'PROJECT_MANAGER']);
            $t->string('pic_name')->nullable()->index(); // PM sees follow-ups assigned to this PIC name
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->text('value')->nullable();
            $t->timestamps();
        });

        Schema::create('data_import_batches', function (Blueprint $t) {
            $t->id();
            $t->string('file_name');
            $t->string('source_kind')->nullable(); // PROJECT_MASTER | PROJECT_UPDATES | PRODUCTION_DATA
            $t->string('file_hash', 64)->nullable();
            $t->dateTime('as_of');
            $t->enum('status', ['PROCESSING', 'COMPLETED', 'COMPLETED_WITH_WARNINGS', 'FAILED', 'REJECTED'])->default('PROCESSING');
            $t->unsignedInteger('total_records')->default(0);
            $t->unsignedInteger('valid_records')->default(0);
            $t->unsignedInteger('invalid_records')->default(0);
            $t->unsignedInteger('new_records')->default(0);
            $t->unsignedInteger('duplicate_records')->default(0);
            $t->unsignedInteger('conflicting_records')->default(0);
            $t->unsignedInteger('missing_field_count')->default(0);
            $t->text('message')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->dateTime('started_at')->nullable();
            $t->dateTime('finished_at')->nullable();
            $t->timestamps();
        });

        Schema::create('projects', function (Blueprint $t) {
            $t->id();
            $t->string('project_code', 50)->unique();
            $t->string('customer');
            $t->string('name');
            $t->string('project_type')->nullable();
            $t->string('pic')->nullable()->index();
            $t->date('start_date')->nullable();
            $t->date('deadline')->nullable();
            $t->decimal('target', 14, 2)->nullable();
            $t->string('target_unit', 30)->nullable();
            $t->decimal('reported_actual', 14, 2)->nullable();
            $t->decimal('reported_progress_pct', 6, 2)->nullable();
            $t->string('reported_status', 50)->nullable()->index();
            $t->dateTime('last_reported_update_at')->nullable();
            $t->unsignedInteger('revision')->default(1);
            $t->string('fingerprint', 40);
            $t->foreignId('source_batch_id')->nullable()->constrained('data_import_batches')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('project_updates', function (Blueprint $t) {
            $t->id();
            $t->string('update_code', 50);
            $t->unsignedInteger('revision')->default(1);
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->dateTime('occurred_at');
            $t->string('source_type', 50);
            $t->string('source_name')->nullable();
            $t->text('message');
            $t->boolean('is_current')->default(true);
            $t->string('fingerprint', 40);
            $t->foreignId('source_batch_id')->nullable()->constrained('data_import_batches')->nullOnDelete();
            $t->timestamps();
            $t->unique(['update_code', 'revision']);
            $t->index(['project_id', 'occurred_at']);
        });

        Schema::create('production_records', function (Blueprint $t) {
            $t->id();
            $t->string('source_record_id', 80);
            $t->unsignedInteger('revision')->default(1);
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->string('source_system', 80);
            $t->string('record_type', 80);
            $t->dateTime('observed_at');
            $t->json('payload'); // heterogeneous per source_system/record_type (data dictionary)
            $t->boolean('is_current')->default(true);
            $t->string('fingerprint', 40);
            $t->foreignId('source_batch_id')->nullable()->constrained('data_import_batches')->nullOnDelete();
            $t->timestamps();
            $t->unique(['source_record_id', 'revision']);
            $t->index(['project_id', 'observed_at']);
        });

        Schema::create('data_validation_errors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('batch_id')->constrained('data_import_batches')->cascadeOnDelete();
            $t->string('source_kind')->nullable();
            $t->string('record_ref')->nullable(); // row number / id
            $t->string('field')->nullable();
            $t->string('code', 50);
            $t->enum('severity', ['ERROR', 'WARNING', 'INFO'])->default('ERROR');
            $t->text('message');
            $t->timestamps();
            $t->index(['batch_id', 'severity']);
        });

        Schema::create('evidence_records', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->enum('evidence_class', ['REPORTED', 'OPERATIONAL']);
            $t->string('category', 40);        // PRODUCTION_OUTPUT, ENVIRONMENT, DEPLOYMENT, USAGE, EQUIPMENT, REPORT, ...
            $t->string('origin_table', 30);    // projects | project_updates | production_records
            $t->unsignedBigInteger('origin_id');
            $t->string('source_ref', 100);     // update_id / source_record_id / project master ref
            $t->string('source_system', 80)->nullable(); // source_system or source_type
            $t->string('source_label')->nullable();
            $t->unsignedInteger('revision')->default(1);
            $t->dateTime('observed_at');
            $t->string('metric_key', 80);
            $t->string('subject_key', 80)->default(''); // e.g. scanner id
            $t->string('observed_value')->nullable();
            $t->decimal('numeric_value', 16, 2)->nullable();
            $t->string('unit', 30)->nullable();
            $t->text('summary')->nullable();
            $t->string('signals')->nullable();  // lexicon signals for message evidence
            $t->boolean('is_current')->default(true);   // false = superseded by a newer value of same subject/metric
            $t->boolean('is_stale')->default(false);
            $t->boolean('is_duplicate')->default(false);
            $t->string('conflict_group', 40)->nullable()->index();
            $t->string('fingerprint', 40)->unique();
            $t->timestamps();
            $t->index(['project_id', 'observed_at']);
            $t->index(['project_id', 'metric_key', 'subject_key']);
        });
    }

    public function down(): void
    {
        foreach (['evidence_records', 'data_validation_errors', 'production_records', 'project_updates', 'projects', 'data_import_batches', 'settings', 'users'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};

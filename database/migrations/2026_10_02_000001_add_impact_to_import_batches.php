<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('data_import_batches', function (Blueprint $t) {
            $t->string('origin', 20)->default('FILE')->after('file_name'); // FILE | MANUAL
            $t->longText('impact')->nullable()->after('message');          // before/after summary of the analysis
        });
    }

    public function down(): void
    {
        Schema::table('data_import_batches', fn (Blueprint $t) => $t->dropColumn(['origin', 'impact']));
    }
};

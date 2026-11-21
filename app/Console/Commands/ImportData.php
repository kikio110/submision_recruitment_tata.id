<?php
namespace App\Console\Commands;

use App\Services\DataImportService;
use Illuminate\Console\Command;

class ImportData extends Command
{
    protected $signature = 'ontrack:import {files* : CSV/JSON paths} {--as-of= : Reference date (Y-m-d H:i:s)}';
    protected $description = 'Import project master/update CSV and production JSON files through the same pipeline as the UI';

    public function handle(DataImportService $svc): int
    {
        $files = array_map(fn ($f) => ['path' => $f, 'name' => basename($f)], $this->argument('files'));
        foreach ($svc->importFiles($files, $this->option('as-of')) as $b) {
            $this->line(sprintf('%-28s %-24s total=%d valid=%d invalid=%d new=%d dup=%d conflict=%d', $b->file_name, $b->status, $b->total_records, $b->valid_records, $b->invalid_records, $b->new_records, $b->duplicate_records, $b->conflicting_records));
        }
        return self::SUCCESS;
    }
}

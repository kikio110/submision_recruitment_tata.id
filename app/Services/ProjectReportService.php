<?php
namespace App\Services;

use App\Models\{Project, Setting};
use App\Support\CurrentUser;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * PIC / PM reporting. A report becomes a REPORTED update (timeline + evidence) through the same
 * import pipeline as files, so interpretation, attention and re-verification run automatically.
 * A report is never operational evidence: it cannot, by itself, verify an issue.
 */
class ProjectReportService
{
    public const SOURCES = ['PROJECT_MANAGER' => 'Project Manager', 'TECHNICAL_TEAM' => 'Tim teknis', 'FIELD_TEAM' => 'Tim lapangan', 'OPERATOR' => 'Operator', 'ACCOUNT' => 'Account', 'VENDOR' => 'Vendor'];

    public function __construct(private DataImportService $import) {}

    public function canReport(?\App\Models\User $u, Project $p): bool
    {
        return $u && ($u->isDirector() || ($u->pic_name && $p->pic && strcasecmp($u->pic_name, $p->pic) === 0));
    }

    /** @return string[] human readable outcomes */
    public function report(Project $p, array $d, ?string $prefix = null): array
    {
        $at = \Carbon\CarbonImmutable::parse($d['reported_at'])->format('Y-m-d\TH:i:s');
        $out = [];
        $hasProgress = ($d['reported_progress_pct'] ?? '') !== '' || ($d['reported_actual'] ?? '') !== '' || ($d['reported_status'] ?? '') !== '';
        if ($hasProgress) {
            $row = $this->masterRow($p);
            foreach (['reported_progress_pct', 'reported_actual', 'reported_status'] as $k) if (($d[$k] ?? '') !== '') $row[$k] = $d[$k];
            $row['last_reported_update_at'] = $at;
            $b = $this->import->importRows(DataValidationService::KIND_MASTER, [$row], "Laporan progres {$p->project_code}");
            if ($b->status === 'REJECTED' || $b->status === 'FAILED' || $b->invalid_records > 0) $this->fail($b);
            $out[] = $b->duplicate_records ? 'Nilai progres sama dengan data tersimpan (tidak ada perubahan).' : 'Progres dilaporkan diperbarui.';
        }
        $message = trim(($prefix ? "$prefix " : '') . $d['message']);
        $row = ['update_id' => 'UPD-M-' . $p->id . '-' . now()->format('ymdHis') . '-' . Str::upper(Str::random(3)), 'project_id' => $p->project_code, 'timestamp' => $at,
            'source_type' => $d['source_type'] ?? 'PROJECT_MANAGER', 'source_name' => CurrentUser::name(), 'message' => $message];
        $b = $this->import->importRows(DataValidationService::KIND_UPDATES, [$row], "Laporan PIC {$p->project_code}");
        if ($b->status === 'REJECTED' || $b->status === 'FAILED' || $b->invalid_records > 0) $this->fail($b);
        $out[] = 'Laporan masuk ke timeline sebagai informasi yang dilaporkan.';
        ActivityLogService::log('PIC_REPORT', "Laporan dari " . CurrentUser::name() . " pada {$p->project_code}: " . Str::limit($message, 120), $p->id);
        return $out;
    }

    public function masterRow(Project $p): array
    {
        return ['project_id' => $p->project_code, 'customer' => $p->customer, 'project_name' => $p->name, 'project_type' => $p->project_type, 'pic' => $p->pic,
            'start_date' => $p->start_date?->toDateString(), 'deadline' => $p->deadline?->toDateString(), 'target' => $p->target, 'target_unit' => $p->target_unit,
            'reported_actual' => $p->reported_actual, 'reported_progress_pct' => $p->reported_progress_pct, 'reported_status' => $p->reported_status,
            'last_reported_update_at' => $p->last_reported_update_at?->format('Y-m-d\TH:i:s')];
    }

    private function fail($batch): void
    {
        $msg = $batch->errors()->where('severity', 'ERROR')->first()?->message ?? $batch->message ?? 'Data tidak dapat diproses.';
        throw ValidationException::withMessages(['message' => $msg]);
    }

    public static function defaultReportTime(): string { return Setting::asOf()->format('Y-m-d\TH:i'); }
}

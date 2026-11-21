<?php
namespace App\Services;

use App\Models\Finding;
use App\Services\Interpretation\Rules;
use Illuminate\Support\Collection;

/** Deterministic, rule-based interpretation (FR-006). Rules are generic; no project ids. */
class InterpretationService
{
    /** @return array<int,\App\Services\Interpretation\Rule> */
    public function rules(): array
    {
        return [new Rules\ScheduleDeviationRule, new Rules\ProgressDiscrepancyRule, new Rules\PendingQuantityRule, new Rules\OperationalStatusRule,
            new Rules\MissingPrerequisiteRule, new Rules\StaleEvidenceRule, new Rules\ConflictingEvidenceRule, new Rules\DataQualityRule,
            new Rules\ReportedBlockerRule, new Rules\UsageHealthRule, new Rules\InsufficientEvidenceRule];
    }

    public static function fingerprint(string $rule, string $subject): string { return sha1($rule . '|' . $subject); }

    /** Pure evaluation: drafts keyed by fingerprint. */
    public function evaluate(ProjectContext $ctx): array
    {
        $out = [];
        foreach ($this->rules() as $rule) {
            foreach ($rule->evaluate($ctx) as $d) {
                $d['fingerprint'] = self::fingerprint($d['rule_key'], $d['subject_key']);
                $out[$d['fingerprint']] = $d;
            }
        }
        return $out;
    }

    /** Persist findings, preserving identity (and thus follow-ups) across refreshes. */
    public function interpret(ProjectContext $ctx, ?int $batchId = null): Collection
    {
        $project = $ctx->project; $drafts = $this->evaluate($ctx); $now = now();
        $existing = Finding::where('project_id', $project->id)->get()->keyBy('fingerprint');
        $result = collect();
        foreach ($drafts as $fp => $d) {
            $f = $existing->get($fp);
            $attrs = ['category' => $d['category'], 'severity' => $d['severity'], 'title' => $d['title'], 'description' => $d['description'],
                'relevant_metrics' => implode(',', $d['metrics']), 'last_detected_at' => $now, 'is_active' => true, 'source_batch_id' => $batchId];
            if (! $f) {
                $f = Finding::create($attrs + ['project_id' => $project->id, 'rule_key' => $d['rule_key'], 'subject_key' => $d['subject_key'], 'fingerprint' => $fp,
                    'baseline_at' => $d['baseline_at'], 'detected_at' => $now, 'verification_status' => 'OPEN']);
            } else {
                if (! $f->is_active) { $attrs += ['baseline_at' => $d['baseline_at'], 'detected_at' => $now]; }
                if (in_array($f->verification_status, ['VERIFIED'])) { $attrs['verification_status'] = 'REOPENED'; }
                $f->update($attrs);
            }
            $f->evidence()->detach();
            foreach ($d['evidence'] as [$e, $role]) $f->evidence()->attach($e->id, ['role' => $role]);
            $result->push($f);
        }
        Finding::where('project_id', $project->id)->where('is_active', true)->whereNotIn('fingerprint', array_keys($drafts))->update(['is_active' => false]);
        return $result;
    }
}

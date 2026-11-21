<?php
namespace Tests\Support;

use App\Models\{Finding, FollowUp, Project, User};
use App\Services\DataImportService;
use App\Support\CurrentUser;

trait DatasetHelpers
{
    protected function seedDataset(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        CurrentUser::set(User::where('role', 'DIRECTOR')->first());
    }

    protected function tmp(string $name, string $content): string
    {
        $p = sys_get_temp_dir() . '/ontrack_' . uniqid() . '_' . $name;
        file_put_contents($p, $content);
        return $p;
    }

    protected function import(string $name, string $content, ?string $asOf = null)
    {
        return app(DataImportService::class)->importFile($this->tmp($name, $content), $name, $asOf);
    }

    protected function project(string $code): Project { return Project::where('project_code', $code)->firstOrFail(); }

    protected function finding(string $code, string $rule, ?string $subject = null): Finding
    {
        return Finding::where('project_id', $this->project($code)->id)->where('rule_key', $rule)
            ->when($subject, fn ($q) => $q->where('subject_key', $subject))->firstOrFail();
    }

    protected function followUpFor(Finding $f, string $pic = 'Nadia'): FollowUp
    {
        return app(\App\Services\FollowUpService::class)->create(['project_id' => $f->project_id, 'finding_id' => $f->id,
            'title' => 'Tindak lanjut ' . $f->title, 'assigned_pic' => $pic, 'priority' => 'HIGH']);
    }

    protected function prodJson(array $records): string { return json_encode(['generated_at' => '2026-10-07T12:00:00', 'records' => $records]); }
}

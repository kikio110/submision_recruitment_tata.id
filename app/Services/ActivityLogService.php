<?php
namespace App\Services;
use App\Models\ActivityLog;
use App\Support\CurrentUser;
class ActivityLogService {
    public static function log(string $type, string $description, ?int $projectId = null, ?int $followUpId = null, ?int $batchId = null, bool $system = false): ActivityLog {
        $u = $system ? null : CurrentUser::get();
        return ActivityLog::create(['type' => $type, 'description' => $description, 'actor_id' => $u?->id,
            'actor_name' => $u?->name ?? 'System', 'project_id' => $projectId, 'follow_up_id' => $followUpId, 'batch_id' => $batchId]);
    }
}

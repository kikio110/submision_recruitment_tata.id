<?php
namespace App\Livewire;

use App\Models\{Finding, Project};
use App\Services\ProjectContextService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PortfolioTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')] public string $search = '';
    #[Url] public string $level = '';
    #[Url] public string $status = '';
    #[Url] public string $pic = '';
    #[Url] public string $sort = 'att_score';
    #[Url] public string $dir = 'desc';
    public int $perPage = 10;

    public function updating($name): void { if (in_array($name, ['search', 'level', 'status', 'pic', 'perPage'])) $this->resetPage(); }

    public function sortBy(string $col): void
    {
        $this->dir = $this->sort === $col && $this->dir === 'asc' ? 'desc' : 'asc';
        $this->sort = $col;
    }

    public function clear(): void { $this->reset(['search', 'level', 'status', 'pic']); $this->resetPage(); }

    public function render(ProjectContextService $contexts)
    {
        $allowed = ['project_code', 'name', 'pic', 'reported_progress_pct', 'deadline', 'att_score', 'latest_update', 'open_findings'];
        $sort = in_array($this->sort, $allowed) ? $this->sort : 'att_score';
        $dir = $this->dir === 'asc' ? 'asc' : 'desc';
        $q = Project::query()
            ->leftJoin('attention_assessments as a', fn ($j) => $j->on('a.project_id', '=', 'projects.id')->where('a.is_current', 1))
            ->select('projects.*', 'a.level as att_level', 'a.score as att_score')
            ->selectRaw("(select count(*) from findings f where f.project_id = projects.id and f.is_active = 1 and f.severity != 'INFO') as open_findings")
            ->selectRaw("(select count(*) from findings f where f.project_id = projects.id and f.verification_status in ('PENDING_VERIFICATION','UNVERIFIED','REOPENED')) as verif_needed")
            ->selectRaw("(select count(*) from findings f where f.project_id = projects.id and f.verification_status = 'VERIFIED') as verif_ok")
            ->selectRaw("greatest(coalesce(projects.last_reported_update_at,'1970-01-01'), coalesce((select max(occurred_at) from project_updates u where u.project_id = projects.id and u.is_current = 1),'1970-01-01'), coalesce((select max(observed_at) from production_records r where r.project_id = projects.id and r.is_current = 1),'1970-01-01')) as latest_update")
            ->when($this->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('projects.project_code', 'like', "%$s%")->orWhere('projects.name', 'like', "%$s%")->orWhere('projects.customer', 'like', "%$s%")))
            ->when($this->level, fn ($q, $l) => $q->where('a.level', $l))
            ->when($this->status, fn ($q, $s) => $q->where('projects.reported_status', $s))
            ->when($this->pic, fn ($q, $p) => $q->where('projects.pic', $p))
            ->orderBy($sort === 'att_score' ? 'att_score' : $sort, $dir)->orderBy('projects.project_code');
        $page = $q->paginate($this->perPage);
        $ctx = $contexts->forProjects($page->pluck('id')->all());
        return view('livewire.portfolio-table', [
            'projects' => $page, 'ctx' => $ctx,
            'statuses' => Project::whereNotNull('reported_status')->distinct()->orderBy('reported_status')->pluck('reported_status'),
            'pics' => Project::whereNotNull('pic')->distinct()->orderBy('pic')->pluck('pic'),
        ]);
    }
}

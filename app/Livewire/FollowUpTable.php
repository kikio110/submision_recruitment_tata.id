<?php
namespace App\Livewire;

use App\Models\{FollowUp, Setting};
use App\Support\CurrentUser;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class FollowUpTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')] public string $search = '';
    #[Url] public string $status = '';
    #[Url] public string $priority = '';
    #[Url] public string $issue = '';
    #[Url] public string $sort = 'due_date';
    #[Url] public string $dir = 'asc';

    public function updating($n): void { $this->resetPage(); }
    public function sortBy(string $c): void { $this->dir = $this->sort === $c && $this->dir === 'asc' ? 'desc' : 'asc'; $this->sort = $c; }

    public function render()
    {
        $u = CurrentUser::get();
        $sort = in_array($this->sort, ['title', 'status', 'priority', 'due_date', 'assigned_pic', 'created_at']) ? $this->sort : 'due_date';
        $q = FollowUp::with(['project', 'finding'])
            ->when($u && ! $u->isDirector(), fn ($q) => $q->where('assigned_pic', $u->pic_name)) // PM sees own follow-ups (FR-015)
            ->when($this->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%$s%")->orWhere('assigned_pic', 'like', "%$s%")->orWhereHas('project', fn ($p) => $p->where('project_code', 'like', "%$s%")->orWhere('name', 'like', "%$s%"))))
            ->when($this->status, fn ($q, $s) => $q->where('status', $s))->when($this->priority, fn ($q, $s) => $q->where('priority', $s))
            ->when($this->issue, fn ($q, $s) => $q->whereHas('finding', fn ($f) => $f->where('verification_status', $s)))
            ->orderBy($sort, $this->dir === 'desc' ? 'desc' : 'asc')->orderByDesc('id');
        return view('livewire.follow-up-table', ['items' => $q->paginate(10), 'isDirector' => (bool) $u?->isDirector(), 'asOf' => Setting::asOf()]);
    }
}

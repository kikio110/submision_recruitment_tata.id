<?php
namespace App\Http\Controllers;

use App\Http\Requests\{ReviewEvidenceRequest, SubmitEvidenceRequest};
use App\Models\{EvidenceSubmission, FollowUp, Project, Setting};
use App\Services\EvidenceSubmissionService as Svc;
use App\Support\CurrentUser;
use Illuminate\Http\Request;

class EvidenceSubmissionController extends Controller
{
    public function __construct(private Svc $svc) {}

    public function create(Request $r, Project $project)
    {
        abort_unless($this->svc->canSubmit(CurrentUser::get(), $project), 403, 'Bukti hanya dapat dikirim oleh PIC proyek atau Director.');
        $fu = $r->follow_up ? FollowUp::where('project_id', $project->id)->with('finding')->find($r->follow_up) : null;
        $pre = ['metric_key' => null, 'subject_key' => null];
        if ($fu?->finding) { // pre-select the metric the original issue is about
            $m = $fu->finding->metrics()[0] ?? null;
            if ($m && $m !== '*') $pre['metric_key'] = $m;
            if (str_contains((string) $fu->finding->subject_key, ':')) $pre['subject_key'] = explode(':', $fu->finding->subject_key, 2)[1];
        }
        return view('pages.evidence.create', ['project' => $project, 'followUp' => $fu, 'metrics' => Svc::metrics(), 'pre' => $pre, 'asOf' => Setting::asOf()]);
    }

    public function store(SubmitEvidenceRequest $r, Project $project)
    {
        $s = $this->svc->submit($project, $r->validated());
        $back = $s->follow_up_id ? route('follow-ups.show', $s->follow_up_id) : route('projects.show', $project);
        return redirect($back)->with('success', $s->status === 'APPROVED' ? 'Bukti diinput Director dan langsung dihitung sebagai evidence operasional. Analisis dihitung ulang.' : 'Bukti diajukan dan menunggu tinjauan Director. Belum dihitung sebagai evidence operasional.');
    }

    public function index(Request $r)
    {
        $u = CurrentUser::get();
        $status = in_array($r->status, ['PENDING', 'APPROVED', 'REJECTED']) ? $r->status : ($r->status === 'ALL' ? null : ($u?->isDirector() ? 'PENDING' : null));
        $q = EvidenceSubmission::with(['project', 'followUp', 'submitter', 'reviewer'])->latest('id')
            ->when($u && ! $u->isDirector(), fn ($q) => $q->whereHas('project', fn ($p) => $p->where('pic', $u->pic_name)))
            ->when($status, fn ($q, $s) => $q->where('status', $s));
        $counts = EvidenceSubmission::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');
        return view('pages.evidence.index', ['items' => $q->paginate(15)->withQueryString(), 'status' => $status, 'counts' => $counts]);
    }

    public function approve(ReviewEvidenceRequest $r, EvidenceSubmission $submission)
    {
        $s = $this->svc->approve($submission, $r->review_note);
        return back()->with('success', "Bukti #{$s->id} disetujui dan dihitung sebagai evidence operasional. Analisis serta verifikasi ulang telah dijalankan.");
    }

    public function reject(ReviewEvidenceRequest $r, EvidenceSubmission $submission)
    {
        $this->svc->reject($submission, $r->review_note);
        return back()->with('success', "Bukti #{$submission->id} ditolak dan tidak dihitung.");
    }
}

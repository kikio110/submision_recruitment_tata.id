<?php
namespace App\Http\Controllers;

use App\Http\Requests\{AddNoteRequest, ChangeStatusRequest, StoreFollowUpRequest, UpdateFollowUpRequest};
use App\Models\{Finding, FollowUp, Project, User};
use App\Services\{FollowUpService, IssueVerificationService};
use App\Support\CurrentUser;

class FollowUpController extends Controller
{
    public function __construct(private FollowUpService $svc) {}

    public function index() { return view('pages.follow-ups.index'); }

    public function create()
    {
        abort_unless(CurrentUser::get()?->isDirector(), 403, 'Hanya Director yang dapat membuat follow-up.');
        $projects = Project::orderBy('project_code')->get(['id', 'project_code', 'name', 'pic']);
        $findings = Finding::where('is_active', true)->where('severity', '!=', 'INFO')->get(['id', 'project_id', 'title', 'severity'])->groupBy('project_id');
        $pics = collect([User::pluck('pic_name'), Project::pluck('pic'), FollowUp::pluck('assigned_pic')])->flatten()->filter()->unique()->values();
        return view('pages.follow-ups.create', compact('projects', 'findings', 'pics'));
    }

    public function store(StoreFollowUpRequest $r)
    {
        $f = $this->svc->create($r->validated());
        return redirect()->route('follow-ups.show', $f)->with('success', 'Follow-up berhasil dibuat dan ditugaskan.');
    }

    public function show(FollowUp $followUp)
    {
        $followUp->load(['project', 'finding.evidence', 'histories', 'creator', 'updater', 'evidenceSubmissions']);
        $verification = $followUp->finding ? $followUp->finding->verifications()->with(['previousEvidence', 'latestEvidence'])->first() : null;
        $pics = collect([User::pluck('pic_name'), Project::pluck('pic'), FollowUp::pluck('assigned_pic')])->flatten()->filter()->unique()->values();
        return view('pages.follow-ups.show', ['f' => $followUp, 'verification' => $verification, 'canModify' => $this->svc->canModify(CurrentUser::get(), $followUp),
            'transitions' => FollowUpService::TRANSITIONS[$followUp->status], 'pics' => $pics]);
    }

    public function update(UpdateFollowUpRequest $r, FollowUp $followUp)
    {
        $this->svc->update($followUp, $r->validated());
        return back()->with('success', 'Follow-up diperbarui.');
    }

    public function status(ChangeStatusRequest $r, FollowUp $followUp)
    {
        $this->svc->changeStatus($followUp, $r->status, $r->note);
        $msg = $r->status === 'RESOLVED' ? 'Follow-up ditandai RESOLVED. Status issue dinilai terpisah berdasarkan evidence — lihat panel verifikasi.' : 'Status follow-up diperbarui.';
        return back()->with('success', $msg);
    }

    public function note(AddNoteRequest $r, FollowUp $followUp)
    {
        $this->svc->addNote($followUp, $r->note);
        $msg = 'Catatan progres ditambahkan.';
        if ($r->boolean('report_to_timeline')) {
            // also report it to the project timeline as a REPORTED update (never operational evidence)
            $project = $followUp->project;
            app(\App\Services\ProjectReportService::class)->report($project, ['message' => $r->note, 'reported_at' => \App\Models\Setting::asOf()->format('Y-m-d\TH:i:s'),
                'source_type' => 'PROJECT_MANAGER'], "[Follow-up #{$followUp->id}: {$followUp->title}]");
            $msg .= ' Catatan juga dilaporkan ke timeline proyek sebagai informasi yang dilaporkan (belum menjadi bukti operasional).';
        }
        return back()->with('success', $msg);
    }

    public function verify(FollowUp $followUp, IssueVerificationService $svc)
    {
        abort_unless($this->svc->canModify(CurrentUser::get(), $followUp), 403);
        abort_unless($followUp->finding, 422, 'Follow-up ini tidak terkait temuan.');
        $v = $svc->verify($followUp->finding, $followUp, 'MANUAL');
        return back()->with('success', "Verifikasi ulang selesai: {$v->result}.");
    }
}

<?php
namespace App\Http\Requests;

use App\Services\ProjectReportService;
use App\Support\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectReportRequest extends FormRequest
{
    public function authorize(): bool { return app(ProjectReportService::class)->canReport(CurrentUser::get(), $this->route('project')); }

    public function rules(): array
    {
        return ['message' => ['required', 'string', 'min:10', 'max:2000'], 'reported_at' => ['required', 'date'],
            'source_type' => ['required', Rule::in(array_keys(ProjectReportService::SOURCES))],
            'reported_progress_pct' => ['nullable', 'numeric', 'between:0,100'], 'reported_actual' => ['nullable', 'numeric', 'min:0'], 'reported_status' => ['nullable', 'string', 'max:50']];
    }

    public function messages(): array
    {
        return ['message.required' => 'Isi laporan wajib diisi.', 'message.min' => 'Laporan terlalu singkat (minimal 10 karakter).', 'reported_at.required' => 'Waktu laporan wajib diisi.',
            'reported_progress_pct.between' => 'Progress harus 0–100.'];
    }
}

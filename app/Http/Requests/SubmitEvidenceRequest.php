<?php
namespace App\Http\Requests;

use App\Models\Setting;
use App\Services\EvidenceSubmissionService as Svc;
use App\Support\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitEvidenceRequest extends FormRequest
{
    public function authorize(): bool { return app(Svc::class)->canSubmit(CurrentUser::get(), $this->route('project')); }

    public function rules(): array
    {
        $metric = (string) $this->input('metric_key');
        $value = Svc::isNumeric($metric) ? ['required', 'numeric', 'min:0'] : ['required', 'string', 'max:60'];
        return [
            'metric_key' => ['required', Rule::in(array_keys(Svc::metrics()))],
            'subject_key' => [Rule::requiredIf($metric === 'scanner_status'), 'nullable', 'string', 'max:60'],
            'observed_value' => $value, 'unit' => ['nullable', 'string', 'max:30'],
            'observed_at' => ['required', 'date', 'before_or_equal:' . Setting::asOf()->toDateTimeString()],
            'source_system' => ['required', 'string', 'max:120'], 'source_note' => ['required', 'string', 'min:5', 'max:1000'],
            'follow_up_id' => ['nullable', Rule::exists('follow_ups', 'id')->where('project_id', $this->route('project')->id)],
        ];
    }

    public function messages(): array
    {
        return ['observed_at.before_or_equal' => 'Waktu observasi tidak boleh melewati tanggal acuan (' . Setting::asOf()->format('d M Y H:i') . '). Ubah tanggal acuan di Settings bila datanya memang lebih baru.',
            'subject_key.required' => 'Subjek wajib untuk status scanner (mis. SCN-A).', 'source_note.required' => 'Jelaskan asal bukti (log, tangkapan layar, nomor tiket, dll.).',
            'source_system.required' => 'Sebutkan sistem asal observasi.', 'observed_value.numeric' => 'Nilai harus berupa angka untuk metrik ini.'];
    }
}

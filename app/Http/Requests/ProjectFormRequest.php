<?php
namespace App\Http\Requests;

use App\Support\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectFormRequest extends FormRequest
{
    public function authorize(): bool { return (bool) CurrentUser::get()?->isDirector(); }

    public function rules(): array
    {
        $editing = $this->route('project') !== null;
        return [
            'project_id' => $editing ? ['prohibited'] : ['required', 'regex:/^[A-Za-z0-9_-]{2,40}$/', Rule::unique('projects', 'project_code')],
            'project_name' => ['required', 'string', 'max:200'], 'customer' => ['required', 'string', 'max:200'],
            'project_type' => ['nullable', 'string', 'max:100'], 'pic' => ['nullable', 'string', 'max:120'],
            'start_date' => ['required', 'date_format:Y-m-d'], 'deadline' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'target' => ['required', 'numeric', 'gt:0'], 'target_unit' => ['nullable', 'string', 'max:30'],
            'reported_actual' => ['nullable', 'numeric', 'min:0'], 'reported_progress_pct' => ['nullable', 'numeric', 'between:0,100'],
            'reported_status' => ['nullable', 'string', 'max:50'], 'last_reported_update_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return ['project_id.regex' => 'ID proyek hanya huruf, angka, strip, atau garis bawah (2–40 karakter).', 'project_id.unique' => 'ID proyek sudah dipakai.',
            'deadline.after_or_equal' => 'Deadline tidak boleh lebih awal dari tanggal mulai.', 'target.gt' => 'Target harus lebih dari 0.',
            'reported_progress_pct.between' => 'Progress harus 0–100.', 'project_name.required' => 'Nama proyek wajib diisi.', 'customer.required' => 'Customer wajib diisi.'];
    }
}

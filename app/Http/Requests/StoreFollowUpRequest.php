<?php
namespace App\Http\Requests;

use App\Support\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;

class StoreFollowUpRequest extends FormRequest
{
    /** Only Directors create/assign follow-ups (FR-007). */
    public function authorize(): bool { return (bool) CurrentUser::get()?->isDirector(); }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'finding_id' => ['nullable', 'exists:findings,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:4000'],
            'instructions' => ['nullable', 'string', 'max:4000'],
            'assigned_pic' => ['required', 'string', 'max:120'],
            'priority' => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return ['project_id.required' => 'Pilih proyek.', 'title.required' => 'Judul tindakan wajib diisi.', 'assigned_pic.required' => 'PIC wajib diisi.', 'priority.required' => 'Pilih prioritas.', 'due_date.date' => 'Format due date tidak valid.'];
    }
}

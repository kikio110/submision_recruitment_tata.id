<?php
namespace App\Http\Requests;

use App\Services\FollowUpService;
use App\Support\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;

class AddNoteRequest extends FormRequest
{
    public function authorize(): bool { return app(FollowUpService::class)->canModify(CurrentUser::get(), $this->route('followUp')); }
    public function rules(): array { return ['note' => ['required', 'string', 'min:3', 'max:2000'], 'report_to_timeline' => ['nullable', 'boolean']]; }
    public function messages(): array { return ['note.required' => 'Catatan progres tidak boleh kosong.']; }
}

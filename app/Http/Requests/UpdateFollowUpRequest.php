<?php
namespace App\Http\Requests;

use App\Services\FollowUpService;
use App\Support\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFollowUpRequest extends FormRequest
{
    public function authorize(): bool { return app(FollowUpService::class)->canModify(CurrentUser::get(), $this->route('followUp')); }

    public function rules(): array
    {
        $rules = ['title' => ['required', 'string', 'max:200'], 'description' => ['nullable', 'string', 'max:4000'], 'instructions' => ['nullable', 'string', 'max:4000'],
            'priority' => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'], 'due_date' => ['nullable', 'date']];
        // Only Directors reassign.
        if (CurrentUser::get()?->isDirector()) $rules['assigned_pic'] = ['required', 'string', 'max:120'];
        return $rules;
    }
}

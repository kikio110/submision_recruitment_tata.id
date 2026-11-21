<?php
namespace App\Http\Requests;

use App\Models\FollowUp;
use App\Services\FollowUpService;
use App\Support\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusRequest extends FormRequest
{
    public function authorize(): bool { return app(FollowUpService::class)->canModify(CurrentUser::get(), $this->route('followUp')); }
    public function rules(): array { return ['status' => ['required', Rule::in(FollowUp::STATUSES)], 'note' => ['nullable', 'string', 'max:2000']]; }
}

<?php
namespace App\Http\Requests;

use App\Support\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;

class ReviewEvidenceRequest extends FormRequest
{
    public function authorize(): bool { return (bool) CurrentUser::get()?->isDirector(); }

    public function rules(): array
    {
        return ['review_note' => [$this->routeIs('evidence.reject') ? 'required' : 'nullable', 'string', 'min:3', 'max:1000']];
    }

    public function messages(): array { return ['review_note.required' => 'Alasan penolakan wajib diisi.']; }
}

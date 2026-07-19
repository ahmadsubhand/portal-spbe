<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectArtikelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'review_note' => ['required', 'string', 'max:1000'],
        ];
    }
}

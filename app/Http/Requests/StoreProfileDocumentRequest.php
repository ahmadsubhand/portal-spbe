<?php

namespace App\Http\Requests;

use App\Models\ProfileDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreProfileDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(ProfileDocument::TYPES))],
            'title' => ['required', 'string', 'max:150'],
            'document' => [
                'required',
                File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(5 * 1024),
            ],
        ];
    }
}

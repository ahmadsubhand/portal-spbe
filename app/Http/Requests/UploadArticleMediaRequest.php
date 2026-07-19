<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadArticleMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isAsn();
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Pilih gambar yang akan diunggah.',
            'image.image' => 'File harus berupa gambar yang valid.',
            'image.mimes' => 'Format gambar harus JPG, PNG, GIF, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 5 MB.',
        ];
    }
}

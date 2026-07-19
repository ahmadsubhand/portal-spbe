<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveTopicRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['visibility' => $this->input('visibility', 'open')]);
    }

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isAsn();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'category' => ['required', 'string', 'in:Regulasi,Teknis,Basis Pengetahuan,Umum'],
            'content' => ['required', 'string', 'min:20'],
            'visibility' => ['required', 'string', 'in:open,closed'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul topik diskusi wajib diisi.',
            'title.min' => 'Judul minimal harus berisi 10 karakter.',
            'content.required' => 'Isi konten diskusi tidak boleh kosong.',
            'content.min' => 'Berikan deskripsi atau pertanyaan yang lebih detail (minimal 20 karakter).',
            'category.required' => 'Silakan pilih kategori diskusi yang sesuai.',
        ];
    }
}

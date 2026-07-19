<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.public_contact_email' => ['nullable', 'email', 'max:255'],
            'settings.public_support_label' => ['nullable', 'string', 'max:255'],
            'settings.dashboard_embed_url' => ['nullable', 'url', 'max:2048'],
            'settings.knowledge_base_status' => ['nullable', 'string', 'max:255'],
        ];
    }
}

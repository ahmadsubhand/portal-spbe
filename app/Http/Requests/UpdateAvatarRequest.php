<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Dimensions;
use Illuminate\Validation\Rules\File;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                File::image()
                    ->types(['jpg', 'jpeg', 'png', 'webp'])
                    ->max(2 * 1024)
                    ->dimensions(
                        (new Dimensions())
                            ->minWidth(200)
                            ->minHeight(200)
                            ->maxWidth(3000)
                            ->maxHeight(3000)
                    ),
            ],
        ];
    }
}

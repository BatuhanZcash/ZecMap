<?php

namespace App\Http\Requests\Admin;

use App\Models\Community;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Community|null $community */
        $community = $this->route('community');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('communities', 'slug')->ignore($community?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'flag_emoji' => ['nullable', 'string', 'max:16'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Topluluk adı gereklidir.',
            'slug.required' => 'Slug gereklidir.',
            'slug.regex' => 'Slug yalnızca küçük harf, rakam ve tire içerebilir.',
            'slug.unique' => 'Bu slug zaten kullanılıyor.',
        ];
    }
}

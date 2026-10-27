<?php

namespace App\Http\Requests\Admin;

use App\Models\Supporter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupporterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $linkUrl = trim((string) $this->input('link_url', ''));

        $this->merge([
            'link_url' => $linkUrl === '' ? null : $linkUrl,
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'link_label' => ['required', 'string', 'max:100'],
            'link_url' => ['nullable', 'string', 'max:500', 'url'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', Rule::in([Supporter::STATUS_ACTIVE, Supporter::STATUS_INACTIVE])],
            'icon' => ['nullable', 'image', 'max:2048'],
            'remove_icon' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ad gereklidir.',
            'description.required' => 'Açıklama gereklidir.',
            'link_label.required' => 'Etiket gereklidir.',
            'link_url.url' => 'Geçerli bir URL girin (ör. https://example.com).',
            'status.required' => 'Durum seçin.',
            'icon.image' => 'İkon dosyası geçerli bir görsel olmalıdır.',
            'icon.max' => 'İkon en fazla 2 MB olabilir.',
        ];
    }
}

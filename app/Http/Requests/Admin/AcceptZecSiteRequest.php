<?php

namespace App\Http\Requests\Admin;

use App\Models\AcceptZecSite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcceptZecSiteRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'website_url' => ['required', 'string', 'max:500', 'url'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', Rule::in([AcceptZecSite::STATUS_ACTIVE, AcceptZecSite::STATUS_INACTIVE])],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Site adı gereklidir.',
            'description.required' => 'Açıklama gereklidir.',
            'website_url.required' => 'Site URL adresi gereklidir.',
            'website_url.url' => 'Geçerli bir URL girin.',
            'status.required' => 'Durum seçin.',
            'logo.image' => 'Logo dosyası geçerli bir görsel olmalıdır.',
            'logo.max' => 'Logo en fazla 2 MB olabilir.',
        ];
    }
}

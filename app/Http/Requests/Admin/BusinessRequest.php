<?php

namespace App\Http\Requests\Admin;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessRequest extends FormRequest
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
            'status' => ['required', Rule::in([
                Business::STATUS_ACTIVE,
                Business::STATUS_INACTIVE,
                Business::STATUS_CLOSED,
            ])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'verified' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'website' => ['nullable', 'string', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:50'],
            'zcash_address' => ['nullable', 'string', 'max:255'],
            'payment_provider' => ['required', Rule::in([
                Business::PAYMENT_ZCASH,
                Business::PAYMENT_FLEXA,
            ])],
            'social_twitter' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'badge_key' => ['nullable', 'string', 'max:32', Rule::in([
                '',
                ...array_keys(Business::badgePresets()),
                Business::BADGE_CUSTOM,
            ])],
            'badge_label' => ['nullable', 'string', 'max:24', 'required_if:badge_key,custom'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'remove_logo' => ['sometimes', 'boolean'],
            'redirect_search' => ['nullable', 'string', 'max:255'],
            'redirect_status' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'İşletme adı gereklidir.',
            'status.required' => 'Durum seçilmelidir.',
            'payment_provider.required' => 'Ödeme tipi seçilmelidir.',
            'logo.image' => 'Logo geçerli bir görsel dosyası olmalıdır.',
            'logo.max' => 'Logo en fazla 4 MB olabilir.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'verified' => $this->boolean('verified'),
            'remove_logo' => $this->boolean('remove_logo'),
            'category_id' => $this->filled('category_id') ? $this->integer('category_id') : null,
            'lat' => $this->filled('lat') ? $this->input('lat') : null,
            'lng' => $this->filled('lng') ? $this->input('lng') : null,
            'sort_order' => max(0, (int) $this->input('sort_order', 0)),
            'badge_key' => ($badgeKey = trim((string) $this->input('badge_key', ''))) !== ''
                ? $badgeKey
                : null,
            'badge_label' => trim((string) $this->input('badge_label', '')) !== ''
                ? trim((string) $this->input('badge_label'))
                : null,
        ]);
    }
}

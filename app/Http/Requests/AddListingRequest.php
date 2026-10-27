<?php

namespace App\Http\Requests;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AddListingRequest extends FormRequest
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
            'category_slug' => ['required', 'string', 'exists:categories,slug'],
            'description' => ['required', 'string', 'max:2000'],
            'community_id' => ['nullable', 'integer', 'exists:communities,id'],
            'payment_provider' => ['required', Rule::in([
                Business::PAYMENT_ZCASH,
                Business::PAYMENT_FLEXA,
            ])],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['required', 'string', 'max:500'],
            'email' => ['required', 'email', 'max:255'],
            'zcash_address' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:2048'],
            'twitter' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50', 'required_without:telegram'],
            'telegram' => ['nullable', 'string', 'max:100', 'required_without:phone'],
            'logo' => [
                'required',
                'file',
                'max:4096',
                'mimetypes:image/jpeg,image/pjpeg,image/png,image/x-png,image/gif,image/webp,image/bmp',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Business name is required.',
            'category_slug.required' => 'Please choose a category.',
            'description.required' => 'Please add a short description of the business.',
            'payment_provider.required' => 'Payment type is required.',
            'lat.required' => 'Please select a location on the map.',
            'lng.required' => 'Please select a location on the map.',
            'address.required' => 'Address is required.',
            'email.required' => 'Contact email is required.',
            'email.email' => 'Enter a valid contact email.',
            'phone.required_without' => 'Enter a phone number or a Telegram handle (at least one).',
            'telegram.required_without' => 'Enter a Telegram handle or a phone number (at least one).',
            'logo.required' => 'A photo or logo is required.',
            'logo.uploaded' => 'Image upload failed. Please try a smaller file or a different format.',
            'logo.mimetypes' => 'Image must be a JPG, PNG, GIF, or WebP file.',
            'logo.max' => 'Image must be 4 MB or smaller.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $logo = $this->file('logo');

            if (! $logo) {
                return;
            }

            if (! $logo->isValid()) {
                $validator->errors()->add(
                    'logo',
                    'Logo upload failed: '.$logo->getErrorMessage(),
                );
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'community_id' => $this->filled('community_id') ? $this->integer('community_id') : null,
        ]);
    }
}

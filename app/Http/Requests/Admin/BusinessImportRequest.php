<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BusinessImportRequest extends FormRequest
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
            'json_file' => ['required', 'file', 'extensions:json', 'max:51200'],
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
            'json_file.required' => 'JSON dosyası seçilmelidir.',
            'json_file.file' => 'Geçerli bir dosya yükleyin.',
            'json_file.extensions' => 'Dosya .json uzantılı olmalıdır.',
            'json_file.max' => 'Dosya en fazla 50 MB olabilir.',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Models\BlogPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BlogPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var BlogPost|null $blog */
        $blog = $this->route('blog');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blog_posts', 'slug')->ignore($blog?->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'featured_image' => [
                'nullable',
                'file',
                'max:4096',
                'mimetypes:image/jpeg,image/pjpeg,image/png,image/x-png,image/gif,image/webp,image/bmp',
            ],
            'remove_featured_image' => ['sometimes', 'boolean'],
            'status' => ['required', Rule::in([BlogPost::STATUS_DRAFT, BlogPost::STATUS_PUBLISHED])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Başlık gereklidir.',
            'slug.required' => 'Slug gereklidir.',
            'slug.unique' => 'Bu slug zaten kullanılıyor.',
            'slug.regex' => 'Slug yalnızca küçük harf, rakam ve tire içerebilir.',
            'content.required' => 'İçerik gereklidir.',
            'status.required' => 'Durum seçilmelidir.',
            'featured_image.mimetypes' => 'Blog görseli JPG, PNG, GIF veya WebP formatında olmalıdır.',
            'featured_image.max' => 'Blog görseli en fazla 4 MB olabilir.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'remove_featured_image' => $this->boolean('remove_featured_image'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $image = $this->file('featured_image');

            if (! $image) {
                return;
            }

            if (! $image->isValid()) {
                $validator->errors()->add(
                    'featured_image',
                    'Görsel yüklenemedi: '.$image->getErrorMessage(),
                );
            }
        });
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['required', 'string', 'min:10', 'max:1000'],
            'image' => ['nullable', 'image', 'max:4096'],
            'community_id' => ['nullable', 'integer', 'exists:communities,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Please select a rating.',
            'rating.min' => 'Rating must be at least 1 star.',
            'rating.max' => 'Rating cannot exceed 5 stars.',
            'body.required' => 'Please write a review.',
            'body.min' => 'Review must be at least 10 characters.',
            'body.max' => 'Review cannot exceed 1000 characters.',
            'image.image' => 'Review photo must be a valid image file.',
            'image.max' => 'Review photo must be 4 MB or smaller.',
            'community_id.exists' => 'Please select a valid community.',
        ];
    }
}

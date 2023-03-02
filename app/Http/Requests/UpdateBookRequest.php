<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'data' => 'required|array',
            'data.id' => 'required|string',
            'data.type' => 'required|in:books',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'sometimes|required|string',
            'data.attributes.description' => 'sometimes|required|string',
            'data.attributes.publication_year' => 'sometimes|required|string',
        ];
    }
}

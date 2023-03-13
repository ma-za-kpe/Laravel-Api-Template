<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JSONAPIRelationshipRequest extends FormRequest
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
            'data' => 'present|array',
            'data.*.id' => 'required|string',
            'data.*.type' => 'required|in:authors',
        ];

       // 'data.type' => ['required', Rule::in(array_keys(config('jsonapi.resources')))],

    }
}

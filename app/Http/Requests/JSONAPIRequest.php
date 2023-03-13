<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JSONAPIRequest extends FormRequest
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
        $rules = [
            'data' => 'required|array',
            'data.id' => ($this->method() === 'PATCH') ? 'required|string' : 'string',
            'data.type' => ['required', Rule::in(array_keys(config('jsonapi.resources')))],
            'data.attributes' => 'required|array',
        ];
        // $rules = [
        //     'data' => 'required|array',
        //     'data.id' => ($this->method() === 'PATCH') ? 'required|
        //     string' : 'string',
        //     'data.type' => ['required', Rule::in(array_keys(config('
        //     jsonapi.resources')))],
        //     'data.attributes' => 'required|array',
        // ];
        return $this->mergeConfigRules($rules);
    }

    public function mergeConfigRules(array $rules): array
    {
        $type = $this->input('data.type');
        if ($type && config("jsonapi.resources.{$type}")) {
            switch ($this->method) {
                case 'PATCH':
                    $rules = array_merge($rules, config("jsonapi.resources.{$type}.validationRules.update"));
                    break;
                case 'POST':
                default:
                    $rules = array_merge($rules, config("jsonapi.resources.{$type}.validationRules.create"));
                    break;
            }
        }
        return $rules;
    }
}

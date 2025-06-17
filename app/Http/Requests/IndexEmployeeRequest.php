<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexEmployeeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'field' => ['nullable', 'in:name, position, email, active'],
            'order' => ['nullable','in:asc,desc',],
            'perPage' => ['nullable','integer','min:1',],
            // előkészítés kereső/filter mezőkhöz
            'active' => ['nullable','boolean',],
        ];
    }
}

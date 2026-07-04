<?php

namespace App\Http\Requests;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'amount'       => StringHelper::numberFormat($this->amount, false, false),
            'is_available' => $this->boolean('is_available', false),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image'          => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'name'           => 'required|string|max:255',
            'category'       => 'required|string|in:sembako,uang,bbm,medis',
            'amount'         => 'required',
            'provider'       => 'required|string|max:255',
            'description'    => 'required|string|max:255',
            'is_available'   => 'required|boolean',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceUpdateRequest extends FormRequest
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
        $mergeData = [
            'isID' => $this->boolean('isID', false),
        ];

        if ($this->has('amount')) {
            $mergeData['amount'] = StringHelper::numberFormat($this->amount, false, false);
        }

        if ($this->has('is_available')) {
            $mergeData['is_available'] = $this->boolean('is_available');
        }

        $this->merge($mergeData);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'isID'         => 'required|boolean',
            'image'        => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'name'         => 'nullable|string|max:255',
            'category'     => 'nullable|string|in:sembako,uang,bbm,medis',
            'amount'       => 'nullable',
            'provider'     => 'nullable|string|max:255',
            'description'  => 'nullable|string|max:255',
            'is_available' => 'nullable|boolean',
        ];
    }
}

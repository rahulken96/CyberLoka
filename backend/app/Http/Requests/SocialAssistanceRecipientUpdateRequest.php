<?php

namespace App\Http\Requests;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceRecipientUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $mergeData = [
            'isID' => $this->boolean('isID', false),
        ];

        if ($this->has('amount')) {
            $mergeData['amount'] = StringHelper::numberFormat($this->amount, false, false);
        }

        $this->merge($mergeData);
    }

    public function rules(): array
    {
        return [
            'isID'         => 'required|boolean',
            'social_code'  => 'nullable|exists:social_assistances,social_code',
            'family_code'  => 'nullable|exists:head_of_families,family_code',
            'bank'         => 'nullable|string|max:50',
            'account_bank' => 'nullable|string|max:50',
            'amount'       => 'nullable|numeric|min:0',
            'image'        => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'reason'       => 'nullable|string|max:500',
            'status'       => 'nullable|in:pending,approved,rejected',
        ];
    }
}

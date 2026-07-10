<?php

namespace App\Http\Requests;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceRecipientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => StringHelper::numberFormat($this->amount, false, false),
            'status' => $this->status ?? 'pending',
        ]);
    }

    public function rules(): array
    {
        return [
            'social_code'     => 'required|exists:social_assistances,social_code',
            'family_code'     => 'required|exists:head_of_families,family_code',
            'bank'            => 'required|string|max:50',
            'account_bank'    => 'required|string|max:50',
            'amount'          => 'required|numeric|min:1',
            'image'           => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'reason'          => 'nullable|string|max:500',
            'status'          => 'nullable|in:pending,approved,rejected',
        ];
    }

    public function attributes()
    {
        return [
            'social_code'   => 'Kode Bansos',
            'family_code'   => 'Kode Kepala Keluarga',
            'bank'          => 'Nama Bank',
            'account_bank'  => 'Akun Bank',
            'amount'        => 'Jumlah',
            'image'         => 'Bukti Gambar',
            'reason'        => 'Alasan',
            'status'        => 'Status',
        ];
    }
}

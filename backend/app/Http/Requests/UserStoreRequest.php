<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'phone'     => 'required|string|min:11|unique:users,phone',
            'password'  => 'required|string|min:6',
        ];
    }

    public function attributes()
    {
        return [
            'name'      => 'Nama',
            'email'     => 'Email',
            'phone'     => 'No. HP',
            'password'  => 'Kata Sandi',
        ];
    }

    public function messages()
    {
        return [
            'required'      => ':attribute wajib diisi',
            'string'        => ':attribute wajib format string',
            'max'           => ':attribute wajib maksimal :max karakter',
            'min'           => ':attribute wajib minimal :min karakter',
            'unique'        => ':attribute sudah ada sebelumnya',
            'email'         => ':attribute tidak sesuai dengan format email',
        ];
    }
}

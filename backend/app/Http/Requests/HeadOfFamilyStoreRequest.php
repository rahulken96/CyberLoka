<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HeadOfFamilyStoreRequest extends FormRequest
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
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users,email',
            'phone'          => 'required|string|min:11|unique:users,phone',
            'password'       => 'required|string|min:6',
            'date_of_birth'  => 'required|string',
            'image'          => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'occupation'     => 'required|string|',
            'nik'            => 'required|string|',
            'gender'         => 'required|string|in:pria,wanita',
            'martial_status' => 'required|string|in:single,menikah',
        ];
    }

    public function attributes()
    {
        return [
            'name'           => 'Nama',
            'email'          => 'Email',
            'phone'          => 'No. HP',
            'password'       => 'Kata Sandi',
            'date_of_birth'  => 'Tanggal Lahir',
            'image'          => 'Gambar',
            'occupation'     => 'Pekerjaan',
            'nik'            => 'NIK',
            'gender'         => 'Jenis Kelamin',
            'martial_status' => 'Status Pernikahan',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use App\Repositories\HeadOfFamilyRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FamilyMemberStoreRequest extends FormRequest
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
    public function rules(HeadOfFamilyRepositoryInterface $headOfFamilyRepoInter): array
    {
        $headOfFamilyCode = $this->input('family_code') ?? null;
        $headOfFamily = $headOfFamilyRepoInter->getOneData($headOfFamilyCode, false);
        
        if (!$headOfFamily) {
            abort(ResponseHelper::jsonResponse(false, 'Data Kepala Keluarga Tidak Ditemukan!', null, 404));
        }
            
        $userCode = $headOfFamily?->user_code;
        return [
            'family_code'   => 'required|string|uuid',
            'name'          => 'required|string|max:255',

            'email'         => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userCode, 'user_code'),
            ],

            'phone'         => [
                'required',
                'string',
                'min:11',
                Rule::unique('users', 'phone')->ignore($userCode, 'user_code'),
            ],

            'password'       => 'required|string|min:6',
            'date_of_birth'  => 'required|string',
            'image'          => [
                Rule::requiredIf($this->input('role') !== 'kepala'),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
            'occupation'     => 'required|string|',
            'nik'            => 'required|string|',
            'gender'         => 'required|string|in:pria,wanita',
            'martial_status' => 'required|string|in:single,menikah',
            'relation'       => 'required|string|in:ibu,ayah,anak,saudara',
            'role'           => 'required|string|in:kepala,anggota',
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
            'relation'       => 'Hubungan Keluarga',
            'role'           => 'Peran',
        ];
    }
}

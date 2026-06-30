<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use App\Interfaces\FamilyMemberRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FamilyMemberUpdateRequest extends FormRequest
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
            'isID' => $this->boolean('isID', false),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(FamilyMemberRepositoryInterface $familyMemberRepoInter): array
    {
        $familyMemberParam = $this->route('family_mmember');
        $isWithId          = $this->boolean('isID', false);

        $familyMember = $familyMemberRepoInter->getOneData($familyMemberParam, $isWithId);
        if (!$familyMember) {
            abort(ResponseHelper::jsonResponse(false, 'Data Anggota Keluarga Tidak Ditemukan!', null, 404));
        }

        $userCode = $familyMember->user_code;

        return [
            'isID'           => 'required|boolean',
            'name'           => 'nullable|string|max:255',

            'email'          => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userCode, 'user_code'),
            ],

            'phone'          => [
                'nullable',
                'string',
                'min:11',
                Rule::unique('users', 'phone')->ignore($userCode, 'user_code'),
            ],

            'password'       => 'nullable|string|min:6',
            'date_of_birth'  => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'occupation'     => 'nullable|string',
            'nik'            => 'nullable|string',
            'gender'         => 'nullable|string|in:pria,wanita',
            'martial_status' => 'nullable|string|in:single,menikah',
            'relation'       => 'nullable|string|in:ibu,ayah,anak,saudara',
            'role'           => 'nullable|string|in:kepala,anggota',
        ];
    }

    public function attributes()
    {
        return [
            'isID'           => 'Metode Pencarian Data',
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

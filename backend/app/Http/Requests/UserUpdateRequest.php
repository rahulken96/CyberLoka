<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Interfaces\UserRepositoryInterface;
use App\Helpers\ResponseHelper;

class UserUpdateRequest extends FormRequest
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
    public function rules(UserRepositoryInterface $userRepoInter): array
    {
        // Ambil wild_card dari url (user) [PUT|PATCH -> api/user/{user}]
        // Sedangkan kalo mau ngambil dari request body/payload/query/params bisa pake biasa $this->input(nama_field) atau $this->nama_field
        // Catatan : Kalo disini variable ganti ($request) yang biasa di controller jadi ($this)
        $userParam = $this->route('user');

        // Nah ini contoh dari request body (isID)
        $isWithId = $this->boolean('isID', false);
        
        // Cari user untuk mendapatkan primary key (id) menggunakan repository
        $user = $userRepoInter->getOneUser($userParam, $isWithId);
        
        if (!$user) {
            abort(ResponseHelper::jsonResponse(false, 'User Tidak Ditemukan!', null, 404));
        }
            
        $userId = $user?->id;
        return [
            'isID'      => 'required|boolean',
            'name'      => 'nullable|string|max:255',
            'email'     => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone'     => [
                'nullable',
                'string',
                'min:11',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'password'  => 'nullable|string|min:6',
        ];
    }


    public function attributes()
    {
        return [
            'isID'      => 'Metode Pencarian Data',
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
            'boolean'       => ':attribute wajib boolean (true/false)',
            'string'        => ':attribute wajib format string',
            'max'           => ':attribute wajib maksimal :max karakter',
            'min'           => ':attribute wajib minimal :min karakter',
            'unique'        => ':attribute sudah ada sebelumnya',
            'email'         => ':attribute tidak sesuai dengan format email',
        ];
    }
}

<?php

namespace App\Repositories;

use App\Helpers\StringHelper;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use App\Models\HeadOfFamily;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class HeadOfFamilyRepository implements HeadOfFamilyRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $exec)
    {
        $query = HeadOfFamily::where(function($q) use ($search) {
            if ($search) {
                $q->search($search);
            }
        });

        $query->orderByDesc('created_at');

        if ($limit) {
            $query->take($limit);
        }

        if ($exec) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginate(?string $search, ?int $rowsPerPage)
    {
        $query = $this->getAll($search, $rowsPerPage, false);
        return $query->paginate($rowsPerPage);
    }
    
    public function getOneData(string $string, bool $isWithID = false)
    {
        if ($isWithID) {
            if (!ctype_digit($string)) {
                return null;
            }
            return HeadOfFamily::find($string);
        }

        return HeadOfFamily::where('family_code', $string)->first() 
            ?? HeadOfFamily::where('user_code', $string)->first();
    }

    public function create(array $data)
    {
        $userRepo = new UserRepository();
        $user = $userRepo->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => $data['password'],
        ]);

        $headOfFamily = new HeadOfFamily();
        $headOfFamily->user_code      = $user->user_code;
        $headOfFamily->date_of_birth  = $data['date_of_birth'];
        $headOfFamily->image          = $data['image']->store('assets/head-of-families', 'public');
        $headOfFamily->occupation     = $data['occupation'];
        $headOfFamily->nik            = $data['nik'];
        $headOfFamily->gender         = $data['gender'];
        $headOfFamily->martial_status = $data['martial_status'];
        $headOfFamily->save();

        return $headOfFamily;
    }

    public function update(string $string, array $data, bool $isWithID = false)
    {
        $newImage = null;
        $oldImageToDelete = null;

        try {
            //Update HeadOfFamily
            $headOfFamily = $this->getOneData($string, $isWithID);
            if (!$headOfFamily) {
                throw new \Exception('Data Kepala Keluarga tidak ditemukan!', 404);
            }

            if (!empty($data['image'])) {
                $newImage = $data['image']->store('assets/head-of-families', 'public'); // Catat file baru yang telah diupload
                $oldImageToDelete = $headOfFamily->image; // Catat file lama untuk dihapus nanti
                $headOfFamily->image = $newImage;
            }

            if (!empty($data['date_of_birth'])) $headOfFamily->date_of_birth = $data['date_of_birth'];
            if (!empty($data['occupation'])) $headOfFamily->occupation = $data['occupation'];
            if (!empty($data['nik'])) $headOfFamily->nik = $data['nik'];
            if (!empty($data['gender'])) $headOfFamily->gender = $data['gender'];
            if (!empty($data['martial_status'])) $headOfFamily->martial_status = $data['martial_status'];
            $headOfFamily->save();

            //Update User
            $dataUserRepo = [];
            if (!empty($data['name'])) $dataUserRepo['name'] = $data['name'];
            if (!empty($data['email'])) $dataUserRepo['email'] = $data['email'];
            if (!empty($data['phone'])) $dataUserRepo['phone'] = StringHelper::normalizePhone($data['phone']);
            if (!empty($data['password'])) $dataUserRepo['password'] = Hash::make($data['password']);

            if (!empty($dataUserRepo)) {
                $userRepo = new UserRepository();
                $userRepo->update($headOfFamily->user_code, $dataUserRepo, false);
            }

            // Ketika tidak ada error, aman untuk menghapus gambar lama
            if ($oldImageToDelete && Storage::disk('public')->exists($oldImageToDelete)) {
                Storage::disk('public')->delete($oldImageToDelete);
            }

            return $headOfFamily;
        } catch (\Throwable $err) {
            // Hapus gambar baru jika sempat ter-upload tapi ada error
            if ($newImage && Storage::disk('public')->exists($newImage)) {
                Storage::disk('public')->delete($newImage);
            }
            throw $err;
        }
    }

    public function delete(string $string, bool $isWithID = false)
    {
        // Hapus Head Of Family
        $headOfFamily = $this->getOneData($string, $isWithID);
        if (!$headOfFamily) {
            throw new \Exception('Data Kepala Keluarga tidak ditemukan!', 404);
        }
        // Tidak hapus foto karena sistem menerapkan softDeletes
        // if ($headOfFamily->image && Storage::disk('public')->exists($headOfFamily->image)) {
        //     Storage::disk('public')->delete($headOfFamily->image);
        // }
        
        // Hapus user 
        $userRepo = new UserRepository();
        $userRepo->delete($headOfFamily->user_code, false);

        $headOfFamily->delete();

        return $headOfFamily;
    }
}

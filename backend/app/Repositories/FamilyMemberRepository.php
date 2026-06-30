<?php

namespace App\Repositories;

use App\Helpers\StringHelper;
use App\Interfaces\FamilyMemberRepositoryInterface;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\FamilyMember;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FamilyMemberRepository implements FamilyMemberRepositoryInterface
{
    private HeadOfFamilyRepositoryInterface $headOfFamilyRepoInter;
    private UserRepositoryInterface $userRepoInter;

    public function __construct(HeadOfFamilyRepositoryInterface $headOfFamilyRepoInter, UserRepositoryInterface $userRepoInter) {
        $this->headOfFamilyRepoInter = $headOfFamilyRepoInter;
        $this->userRepoInter = $userRepoInter;
    }

    public function getAll(?string $search, ?int $limit, bool $exec)
    {
        $query = FamilyMember::where(function($q) use ($search) {
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
            return FamilyMember::with(['headOfFamily'])->find($string);
        }

        return FamilyMember::findByCode($string);
    }

    public function create(array $data)
    {
        $headOfFamilyCode = $data['family_code'];
        $headOfFamily = $this->headOfFamilyRepoInter->getOneData($headOfFamilyCode, false);
        if (!$headOfFamily) {
            throw new Exception('Data Kepala Keluarga tidak ditemukan!', 404);
        }

        // Upsert user
        $user = $this->userRepoInter->getOneUser($headOfFamily->user_code, false);
        if (!$user) {
            $userRepo = new UserRepository();
            $user = $userRepo->create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => $data['password'],
            ]);
        } else {
            $this->userRepoInter->update($headOfFamily->user_code, [
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => $data['password'],
            ], false);
        }

        // Update HeadOfFamily data hanya jika role anggota adalah 'kepala'
        if (($data['role'] ?? null) === 'kepala') {
            $headOfFamilyData = array_filter([
                'date_of_birth'  => $data['date_of_birth'] ?? null,
                'occupation'     => $data['occupation'] ?? null,
                'nik'            => $data['nik'] ?? null,
                'gender'         => $data['gender'] ?? null,
                'martial_status' => $data['martial_status'] ?? null,
            ]);

            if (!empty($headOfFamilyData)) {
                $headOfFamily = $this->headOfFamilyRepoInter->update($headOfFamilyCode, $headOfFamilyData, false);
            }
        }

        $familyMember = new FamilyMember();
        $familyMember->family_code    = $headOfFamilyCode;
        $familyMember->user_code      = $user->user_code;
        $familyMember->date_of_birth  = $data['date_of_birth'];

        if (($data['role'] ?? null) === 'kepala') {
            $familyMember->image = $headOfFamily->image;
        } else {
            $familyMember->image = isset($data['image']) 
                ? $data['image']->store('assets/family-members', 'public') 
                : null;
        }

        $familyMember->occupation     = $data['occupation'];
        $familyMember->nik            = $data['nik'];
        $familyMember->gender         = $data['gender'];
        $familyMember->martial_status = $data['martial_status'];
        $familyMember->relation       = $data['relation'];
        $familyMember->role           = $data['role'];
        $familyMember->save();

        return $familyMember;
    }

    public function update(string $string, array $data, bool $isWithID = false)
    {
        $newImage        = null;
        $oldImageToDelete = null;

        try {
            $familyMember = $this->getOneData($string, $isWithID);
            if (!$familyMember) {
                throw new Exception('Data Anggota Keluarga tidak ditemukan!', 404);
            }

            // Update FamilyMember fields
            if (!empty($data['date_of_birth'])) $familyMember->date_of_birth   = $data['date_of_birth'];
            if (!empty($data['occupation'])) $familyMember->occupation         = $data['occupation'];
            if (!empty($data['nik'])) $familyMember->nik                       = $data['nik'];
            if (!empty($data['gender'])) $familyMember->gender                 = $data['gender'];
            if (!empty($data['martial_status'])) $familyMember->martial_status = $data['martial_status'];
            if (!empty($data['relation'])) $familyMember->relation             = $data['relation'];
            if (!empty($data['role'])) $familyMember->role                     = $data['role'];

            // Handle image
            if (!empty($data['image'])) {
                if ($familyMember->role === 'kepala') {
                    // Gambar kepala diurus oleh HeadOfFamilyRepository
                    $headOfFamilyData = array_filter([
                        'date_of_birth'  => $data['date_of_birth']  ?? null,
                        'occupation'     => $data['occupation']      ?? null,
                        'nik'            => $data['nik']             ?? null,
                        'gender'         => $data['gender']          ?? null,
                        'martial_status' => $data['martial_status']  ?? null,
                        'image'          => $data['image'],
                    ]);
                    $updatedHof = $this->headOfFamilyRepoInter->update($familyMember->family_code, $headOfFamilyData, false);
                    $familyMember->image = $updatedHof->image;
                } else {
                    $newImage             = $data['image']->store('assets/family-members', 'public');
                    $oldImageToDelete     = $familyMember->image;
                    $familyMember->image  = $newImage;
                }
            } elseif ($familyMember->role === 'kepala') {
                // Sync field lain ke HeadOfFamily tanpa gambar
                $headOfFamilyData = array_filter([
                    'date_of_birth'  => $data['date_of_birth']  ?? null,
                    'occupation'     => $data['occupation']      ?? null,
                    'nik'            => $data['nik']             ?? null,
                    'gender'         => $data['gender']          ?? null,
                    'martial_status' => $data['martial_status']  ?? null,
                ]);
                if (!empty($headOfFamilyData)) {
                    $this->headOfFamilyRepoInter->update($familyMember->family_code, $headOfFamilyData, false);
                }
            }

            $familyMember->save();

            // Update User
            $dataUserRepo = [];
            if (!empty($data['name']))     $dataUserRepo['name']     = $data['name'];
            if (!empty($data['email']))    $dataUserRepo['email']    = $data['email'];
            if (!empty($data['phone']))    $dataUserRepo['phone']    = StringHelper::normalizePhone($data['phone']);
            if (!empty($data['password'])) $dataUserRepo['password'] = Hash::make($data['password']);
            if (!empty($dataUserRepo)) {
                $this->userRepoInter->update($familyMember->user_code, $dataUserRepo, false);
            }

            // Aman hapus gambar lama setelah semua berhasil
            if ($oldImageToDelete && Storage::disk('public')->exists($oldImageToDelete)) {
                Storage::disk('public')->delete($oldImageToDelete);
            }

            return $familyMember;
        } catch (\Throwable $err) {
            // Rollback gambar baru jika sempat ter-upload
            if ($newImage && Storage::disk('public')->exists($newImage)) {
                Storage::disk('public')->delete($newImage);
            }
            throw $err;
        }
    }

    public function delete(string $string, bool $isWithID = false)
    {
        $familyMember = $this->getOneData($string, $isWithID);
        if (!$familyMember) {
            throw new Exception('Data Anggota Keluarga tidak ditemukan!', 404);
        }
        $this->userRepoInter->delete($familyMember->user_code, false);
        
        $familyMember->delete();

        return $familyMember;
    }
}

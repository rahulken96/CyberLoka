<?php

namespace App\Repositories;

use App\Helpers\StringHelper;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use App\Models\HeadOfFamily;
use Throwable;

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
}

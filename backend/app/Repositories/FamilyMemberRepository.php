<?php

namespace App\Repositories;

use App\Helpers\StringHelper;
use App\Interfaces\FamilyMemberRepositoryInterface;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FamilyMemberRepository implements FamilyMemberRepositoryInterface
{
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
            return FamilyMember::find($string);
        }

        return FamilyMember::findByCode($string);
    }
}

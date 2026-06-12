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
}

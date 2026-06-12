<?php

namespace App\Interfaces;

interface HeadOfFamilyRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $exec);
    public function getAllPaginate(?string $search, ?int $rowsPerPage);
}

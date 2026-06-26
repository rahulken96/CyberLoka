<?php

namespace App\Interfaces;

interface FamilyMemberRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $exec);
    public function getAllPaginate(?string $search, ?int $rowsPerPage);
    public function getOneData(string $string, bool $isWithID = false);
}

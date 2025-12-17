<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $exec);
    public function getAllPaginate(?string $search, ?int $rowsPerPage);
    public function getOneUser(string $string, bool $isWithID = false);
    public function create(array $data);
}

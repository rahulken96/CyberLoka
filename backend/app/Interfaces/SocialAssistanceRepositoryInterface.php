<?php

namespace App\Interfaces;

interface SocialAssistanceRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $exec);
    public function getAllPaginate(?string $search, ?int $rowsPerPage);
    public function getOneData(string $string, bool $isWithID = false);
    public function create(array $data);
    public function update(string $string, array $data, bool $isWithID = false);
    public function delete(string $string, bool $isWithID = false);
}

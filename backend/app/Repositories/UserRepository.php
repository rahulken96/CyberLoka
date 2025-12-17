<?php

namespace App\Repositories;

use App\Models\User;
use App\Helpers\StringHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\UserRepositoryInterface;
use Exception;

class UserRepository implements UserRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $exec)
    {
        $query = User::where(function($q) use ($search) {
            if ($search) {
                $q->search($search);
            }
        });

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

    public function getOneUser(string $string, bool $isWithID = false)
    {
        if ($isWithID) {
            return User::find($string);
        }

        return User::where('user_code', $string)->first();
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $newUser = new User();
            $newUser->name = $data['name'];
            $newUser->email = $data['email'];
            $newUser->phone = StringHelper::normalizePhone($data['phone']);
            $newUser->password = Hash::make($data['password']);
            $newUser->save();

            DB::commit();
            return $newUser;
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error('Failed to save user data', [
                'message' => $err?->getMessage(),
                'file' => $err?->getFile(),
                'line' => $err?->getLine(),
            ]);
            throw new Exception($err->getMessage());
        }
    }
}

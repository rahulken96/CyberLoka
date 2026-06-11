<?php

namespace App\Repositories;

use App\Models\User;
use App\Helpers\StringHelper;
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
            if (!ctype_digit($string)) {
                return null;
            }
            return User::find($string);
        }

        return User::where('user_code', $string)->first();
    }

    public function create(array $data)
    {
        $newUser = new User();
        $newUser->name = $data['name'];
        $newUser->email = $data['email'];
        $newUser->phone = StringHelper::normalizePhone($data['phone']);
        $newUser->password = Hash::make($data['password']);
        $newUser->save();

        return $newUser;
    }

    public function update(string $string, array $data, bool $isWithID = false)
    {
        $user = $this->getOneUser($string, $isWithID);
        if (!$user) {
            throw new Exception('User tidak ditemukanzzz!', 404);
        }

        if (!empty($data['name'])) $user->name = $data['name'];
        if (!empty($data['email'])) $user->email = $data['email'];
        if (!empty($data['phone'])) $user->phone = StringHelper::normalizePhone($data['phone']);
        if (!empty($data['password'])) $user->password = Hash::make($data['password']);
        $user->save();

        return $user;
    }

    public function delete(string $string, bool $isWithID = false)
    {
        $user = $this->getOneUser($string, $isWithID);
        if (!$user) {
            throw new Exception('User tidak ditemukanzzz!', 404);
        }

        $user->delete();

        return $user;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\PaginateResource;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\UserRepositoryInterface;
use Throwable;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepoInter;

    public function __construct(UserRepositoryInterface $userRepoInter)
    {
        $this->userRepoInter = $userRepoInter;
    }

    public function index(Request $request)
    {
        try {
            $users = $this->userRepoInter->getAll($request->search, $request->limit, true);
            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Didapatkan', UserResource::collection($users)); //Return untuk banyak data (collection)
        } catch (Throwable $err) {
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    public function indexPaginate(Request $request)
    {
        $rules = [
            'search'         => 'nullable|string',
            'rows_per_page'  => 'required|integer'
        ];

        $messages = [
            'rows_per_page.required'    => 'Jumlah baris per halaman wajib diisi',
            'rows_per_page.integer'     => 'Format jumlah baris per halaman wajib angka',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $search = $request->search ?? null;
            $users = $this->userRepoInter->getAllPaginate($search, $request->rows_per_page);
            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Didapatkan', PaginateResource::make($users, UserResource::class));
        } catch (Throwable $err) {
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $request = $request->validated();
        DB::beginTransaction();

        try {
            $user = $this->userRepoInter->create($request);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'User Berhasil Ditambahkan', new UserResource($user), 201); //Return untuk 1 data (new Resource)
        } catch (Throwable $err) {
            DB::rollBack();
            Log::error('Failed to save user data', [
                'message' => $err?->getMessage(),
                'file' => $err?->getFile(),
                'line' => $err?->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $string)
    {
        try {
            $isWithId = $request->boolean('isID', false);
            $user = $this->userRepoInter->getOneUser($string, $isWithId);
            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'User Tidak Ditemukan!', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data User Ditemukan', new UserResource($user), 200);
        } catch (Throwable $err) {
            Log::error('Failed to save user data', [
                'message' => $err?->getMessage(),
                'file' => $err?->getFile(),
                'line' => $err?->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $string)
    {
        $validated = $request->validated();
        DB::beginTransaction();

        try {
            $isWithId = $request->boolean('isID', false);
            $user = $this->userRepoInter->update($string, $validated, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'User Berhasil Diubah', new UserResource($user), 200);
        } catch (Throwable $err) {
            DB::rollBack();
            Log::error('Failed to update user data', [
                'message' => $err?->getMessage(),
                'file' => $err?->getFile(),
                'line' => $err?->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $string)
    {
        DB::beginTransaction();

        try {
            $isWithId = $request->boolean('isID', false);
            $user = $this->userRepoInter->delete($string, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'User Berhasil Dihapus', new UserResource($user), 200);
        } catch (Throwable $err) {
            DB::rollBack();
            Log::error('Failed to delete user data', [
                'message' => $err?->getMessage(),
                'file' => $err?->getFile(),
                'line' => $err?->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }
}

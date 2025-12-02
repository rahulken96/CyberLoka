<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

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
            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Didapatkan', UserResource::collection($users));
        } catch (\Exception $err) {
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    public function indexPaginate(Request $request)
    {
        $request->validate([
            'search'    => 'nullable|string',
            'rows'      => 'required|integer'
        ],[
            'rows.required'     => 'Rows wajib diisi',
            'rows.integer'      => 'Format Rows wajib angka',
        ]);

        try {
            $search = $request->search ?? null;
            $users = $this->userRepoInter->getAllPaginate($search, $request->rows);
            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Didapatkan', PaginateResource::make($users, UserResource::class));
        } catch (\Exception $err) {
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

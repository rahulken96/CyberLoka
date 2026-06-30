<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\FamilyMemberStoreRequest;
use App\Http\Requests\FamilyMemberUpdateRequest;
use App\Http\Resources\FamilyMemberResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\FamilyMemberRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FamilyMemberController extends Controller
{
    private FamilyMemberRepositoryInterface $familyMemberRepoInter;

    public function __construct(FamilyMemberRepositoryInterface $familyMemberRepoInter)
    {
        $this->familyMemberRepoInter = $familyMemberRepoInter;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $headOfFamilies = $this->familyMemberRepoInter->getAll($request->search, $request->limit, true);
            return ResponseHelper::jsonResponse(true, 'Data Anggota Keluarga Berhasil Didapatkan', FamilyMemberResource::collection($headOfFamilies));
        } catch (\Throwable $err) {
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
            $headOfFamilies = $this->familyMemberRepoInter->getAllPaginate($search, $request->rows_per_page);
            return ResponseHelper::jsonResponse(true, 'Data Anggota Keluarga Berhasil Didapatkan', PaginateResource::make($headOfFamilies, FamilyMemberResource::class));
        } catch (\Throwable $err) {
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FamilyMemberStoreRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $familyMember = $this->familyMemberRepoInter->create($data);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Anggota Keluarga Berhasil Ditambahkan', new FamilyMemberResource($familyMember), 201);
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to save family member data', [
                'message' => $err->getMessage(),
                'file'    => $err->getFile(),
                'line'    => $err->getLine(),
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
            $familyMember = $this->familyMemberRepoInter->getOneData($string, $isWithId);
            if (!$familyMember) {
                return ResponseHelper::jsonResponse(false, 'Data Anggota Keluarga Tidak Ditemukan!', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data Anggota Keluarga Ditemukan', new FamilyMemberResource($familyMember), 200);
        } catch (\Throwable $err) {
            Log::error('Failed to get family member data', [
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
    public function update(FamilyMemberUpdateRequest $request, string $familyMember)
    {
        $data    = $request->validated();
        $isWithId = $request->boolean('isID', false);

        DB::beginTransaction();
        try {
            $result = $this->familyMemberRepoInter->update($familyMember, $data, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Anggota Keluarga Berhasil Diperbarui', new FamilyMemberResource($result));
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to update family member data', [
                'message' => $err->getMessage(),
                'file'    => $err->getFile(),
                'line'    => $err->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, $err->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $familyMember)
    {
        $isWithId = $request->boolean('isID', false);

        DB::beginTransaction();
        try {
            $result = $this->familyMemberRepoInter->delete($familyMember, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Anggota Keluarga Berhasil Dihapus', new FamilyMemberResource($result));
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to delete family member data', [
                'message' => $err->getMessage(),
                'file'    => $err->getFile(),
                'line'    => $err->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, $err->getCode() ?: 500);
        }
    }
}

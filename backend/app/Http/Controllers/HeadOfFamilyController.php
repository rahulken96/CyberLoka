<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\HeadOfFamilyStoreRequest;
use App\Http\Requests\HeadOfFamilyUpdateRequest;
use App\Http\Resources\HeadOfFamilyResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HeadOfFamilyController extends Controller
{
    private HeadOfFamilyRepositoryInterface $headOfFamilyRepoInter;

    public function __construct(HeadOfFamilyRepositoryInterface $headOfFamilyRepoInter)
    {
        $this->headOfFamilyRepoInter = $headOfFamilyRepoInter;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $headOfFamilies = $this->headOfFamilyRepoInter->getAll($request->search, $request->limit, true);
            return ResponseHelper::jsonResponse(true, 'Data Kepala Keluarga Berhasil Didapatkan', HeadOfFamilyResource::collection($headOfFamilies));
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
            $headOfFamilies = $this->headOfFamilyRepoInter->getAllPaginate($search, $request->rows_per_page);
            return ResponseHelper::jsonResponse(true, 'Data Kepala Keluarga Berhasil Didapatkan', PaginateResource::make($headOfFamilies, HeadOfFamilyResource::class));
        } catch (\Throwable $err) {
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HeadOfFamilyStoreRequest $request)
    {
        $request = $request->validated();
        DB::beginTransaction();

        try {
            $headOfFamily = $this->headOfFamilyRepoInter->create($request);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Kepala Keluarga Berhasil Ditambahkan', new HeadOfFamilyResource($headOfFamily), 201);
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to save head of family data', [
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
            $headOfFamily = $this->headOfFamilyRepoInter->getOneData($string, $isWithId);
            if (!$headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Data Kepala Keluarga Tidak Ditemukan!', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data Kepala Keluarga Ditemukan', new HeadOfFamilyResource($headOfFamily), 200);
        } catch (\Throwable $err) {
            Log::error('Failed to get head of family data', [
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
    public function update(HeadOfFamilyUpdateRequest $request, string $string)
    {
        $validated = $request->validated();
        DB::beginTransaction();

        try {
            $isWithId = $request->boolean('isID', false);
            $user = $this->headOfFamilyRepoInter->update($string, $validated, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Kepala Keluarga Berhasil Diubah', new HeadOfFamilyResource($user), 200);
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to update head of family data', [
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
            $user = $this->headOfFamilyRepoInter->delete($string, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Kepala Keluarga Berhasil Dihapus', new HeadOfFamilyResource($user), 200);
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to delete head of family data', [
                'message' => $err?->getMessage(),
                'file' => $err?->getFile(),
                'line' => $err?->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }
}

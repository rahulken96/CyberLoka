<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\HeadOfFamilyResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use Illuminate\Http\Request;
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
            return ResponseHelper::jsonResponse(true, 'Data User Berhasil Didapatkan', PaginateResource::make($headOfFamilies, HeadOfFamilyResource::class));
        } catch (\Throwable $err) {
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

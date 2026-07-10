<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\SocialAssistanceRecipientResource;
use App\Interfaces\SocialAssistanceRecipientRepositoryInterface;
use App\Http\Requests\SocialAssistanceRecipientStoreRequest;
use App\Http\Requests\SocialAssistanceRecipientUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SocialAssistanceRecipientController extends Controller
{
    private SocialAssistanceRecipientRepositoryInterface $socialAssistRecipientRepoInter;

    public function __construct(SocialAssistanceRecipientRepositoryInterface $socialAssistRecipientRepoInter)
    {
        $this->socialAssistRecipientRepoInter = $socialAssistRecipientRepoInter;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $socialAssists = $this->socialAssistRecipientRepoInter->getAll($request->search, $request->limit, true);
            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos Berhasil Didapatkan', SocialAssistanceRecipientResource::collection($socialAssists));
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
            $socialAssists = $this->socialAssistRecipientRepoInter->getAllPaginate($search, $request->rows_per_page);
            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos Berhasil Didapatkan', PaginateResource::make($socialAssists, SocialAssistanceRecipientResource::class));
        } catch (\Throwable $err) {
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SocialAssistanceRecipientStoreRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        
        try {
            $recipient = $this->socialAssistRecipientRepoInter->create($data);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos Berhasil Ditambahkan', new SocialAssistanceRecipientResource($recipient), 201);
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to save recipient`s social assist data', [
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
            $socialAssist = $this->socialAssistRecipientRepoInter->getOneData($string, $isWithId);
            if (!$socialAssist) {
                return ResponseHelper::jsonResponse(false, 'Data Penerima Bansos Tidak Ditemukan!', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos Ditemukan', new SocialAssistanceRecipientResource($socialAssist), 200);
        } catch (\Throwable $err) {
            Log::error('Failed to get recipient`s social assist data', [
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
    public function update(SocialAssistanceRecipientUpdateRequest $request, string $socialAssistRecipient)
    {
        $data     = $request->validated();
        $isWithId = $request->boolean('isID', false);
        DB::beginTransaction();

        try {
            $result = $this->socialAssistRecipientRepoInter->update($socialAssistRecipient, $data, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Bansos Berhasil Diperbarui', new SocialAssistanceRecipientResource($result));
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to update recipient`s social assist data', [
                'message' => $err->getMessage(),
                'file'    => $err->getFile(),
                'line'    => $err->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $socialAssistRecipient)
    {
        $isWithId = $request->boolean('isID', false);
        DB::beginTransaction();

        try {
            $result = $this->socialAssistRecipientRepoInter->delete($socialAssistRecipient, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos Berhasil Dihapus', new SocialAssistanceRecipientResource($result));
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to delete recipient`s social assist data', [
                'message' => $err->getMessage(),
                'file'    => $err->getFile(),
                'line'    => $err->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }
}

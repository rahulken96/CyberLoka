<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    private EventRepositoryInterface $eventRepoInter;

    public function __construct(EventRepositoryInterface $eventRepoInter)
    {
        $this->eventRepoInter = $eventRepoInter;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $result = $this->eventRepoInter->getAll($request->search, $request->limit, true);
            return ResponseHelper::jsonResponse(true, 'Data Acara Berhasil Didapatkan', EventResource::collection($result));
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
            $results = $this->eventRepoInter->getAllPaginate($search, $request->rows_per_page);
            return ResponseHelper::jsonResponse(true, 'Data Acara Berhasil Didapatkan', PaginateResource::make($results, EventResource::class));
        } catch (\Throwable $err) {
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventStoreRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $result = $this->eventRepoInter->create($data);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Acara Berhasil Ditambahkan', new EventResource($result), 201);
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to save event data', [
                'message' => $err->getMessage(),
                'file'    => $err->getFile(),
                'line'    => $err->getLine(),
            ]);
            if (in_array($err->getCode(), [409, 23000])) {
                return ResponseHelper::jsonResponse(false, "Data Acara Yang Sama Sudah Ada Pada Tanggal Yang Sama Sebelumnya!", null, 409);
            }
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
            $result = $this->eventRepoInter->getOneData($string, $isWithId);
            if (!$result) {
                return ResponseHelper::jsonResponse(false, 'Data Acara Tidak Ditemukan!', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data Acara Ditemukan', new EventResource($result), 200);
        } catch (\Throwable $err) {
            Log::error('Failed to get event data', [
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
    public function update(EventUpdateRequest $request, string $event)
    {
        $data     = $request->validated();
        $isWithId = $request->boolean('isID', false);

        DB::beginTransaction();
        try {
            $result = $this->eventRepoInter->update($event, $data, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Acara Berhasil Diperbarui', new EventResource($result));
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to update event data', [
                'message' => $err->getMessage(),
                'file'    => $err->getFile(),
                'line'    => $err->getLine(),
            ]);
            if (in_array($err->getCode(), [409, 23000])) {
                return ResponseHelper::jsonResponse(false, "Data Acara Yang Sama Sudah Ada Pada Tanggal Yang Sama Sebelumnya!", null, 409);
            }
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $event)
    {
        $isWithId = $request->boolean('isID', false);

        DB::beginTransaction();
        try {
            $result = $this->eventRepoInter->delete($event, $isWithId);
            DB::commit();
            return ResponseHelper::jsonResponse(true, 'Data Acara Berhasil Dihapus', new EventResource($result));
        } catch (\Throwable $err) {
            DB::rollBack();
            Log::error('Failed to delete event data', [
                'message' => $err->getMessage(),
                'file'    => $err->getFile(),
                'line'    => $err->getLine(),
            ]);
            return ResponseHelper::jsonResponse(false, $err->getMessage(), null, 500);
        }
    }
}

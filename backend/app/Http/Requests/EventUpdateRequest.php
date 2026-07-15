<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use App\Interfaces\EventRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $merge = [];
        if ($this->price !== null) {
            $merge['price'] = $this->price;
        }
        if ($this->description !== null) {
            $merge['description'] = $this->description;
        }
        if ($this->is_active !== null) {
            $merge['is_active'] = filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN);
        }
        $this->merge($merge);
    }

    public function rules(EventRepositoryInterface $eventRepoInter): array
    {
        $dataParam = $this->route('event');
        $isWithId = $this->boolean('isID', false);
        $result = $eventRepoInter->getOneData($dataParam, $isWithId);
        if (!$result) {
            abort(ResponseHelper::jsonResponse(false, 'Acara Tidak Ditemukan!', null, 404));
        }
        $dataId        = $result?->id;
        $dataDateEvent = $result?->date_event;
        return [
            'image'         => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'name'          => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('events', 'name')
                    ->withoutTrashed()
                    ->where(function ($query) use ($dataDateEvent) {
                        return $query->whereDate('date_event', $this->date_event ?? $dataDateEvent);
                    })
                    ->ignore($dataId)
            ],
            'description'   => 'nullable|string',
            'price'         => 'nullable|numeric|min:0',
            'date_event'    => 'nullable|date',
            'is_active'     => 'nullable|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'image'         => 'Gambar Acara',
            'name'          => 'Nama Acara',
            'description'   => 'Deskripsi Acara',
            'price'         => 'Harga Tiket',
            'date_event'    => 'Tanggal Acara',
            'is_active'     => 'Status Aktif',
        ];
    }
}

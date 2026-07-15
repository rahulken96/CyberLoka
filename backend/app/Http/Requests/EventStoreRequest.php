<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'price'       => $this->price ?? 0,
            'description' => $this->description ?? null,
            'is_active'   => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'image'         => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'name'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('events', 'name')
                    ->withoutTrashed()
                    ->where(function ($query) {
                        return $query->whereDate('date_event', $this->date_event);
                    })
            ],
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'date_event'    => 'required|date',
            'is_active'     => 'required|boolean',
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

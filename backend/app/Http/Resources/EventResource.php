<?php

namespace App\Http\Resources;

use App\Helpers\StringHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'event_code'    => $this->event_code,
            'image'         => $this->image,
            'name'          => $this->name,
            'description'   => $this->description,
            'price'         => $this->price,
            'price_label'   => StringHelper::numberFormat($this->price, true, true),
            'date_event'    => StringHelper::dateTranslatedFormat($this->date_event, 'l, d M Y H:i'),
            'is_active'     => $this->is_active,
            'created_at'    => StringHelper::dateTranslatedFormat($this->created_at, 'l, d M Y H:i'),
            'updated_at'    => StringHelper::dateTranslatedFormat($this->updated_at, 'l, d M Y H:i'),
        ];
    }
}

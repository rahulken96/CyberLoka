<?php

namespace App\Http\Resources;

use App\Helpers\StringHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAssistanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'social_code'        => $this->social_code,
            'image'              => $this->image ?? null,
            'name'               => $this->name,
            'category'           => $this->category ?? null,
            'amount_label'       => StringHelper::numberFormat($this->amount ?? 0, true),
            'amount'             => $this->amount ?? 0,
            'provider'           => $this->provider ?? null,
            'description'        => $this->description,
            'is_available'       => $this->is_available,
            'created_at'         => StringHelper::dateTranslatedFormat($this->created_at, 'l, d M Y H:i'),
            'updated_at'         => StringHelper::dateTranslatedFormat($this->updated_at, 'l, d M Y H:i'),
        ];
    }
}

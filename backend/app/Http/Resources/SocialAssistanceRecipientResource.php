<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAssistanceRecipientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'social_recipient_code' => $this->social_recipient_code,
            'social_code'           => new SocialAssistanceResource($this->whenLoaded('socialAssistance')),
            'family_code'           => new HeadOfFamilyResource($this->whenLoaded('headOfFamily')),
            'bank'                  => $this->bank,
            'account_bank'          => $this->account_bank,
            'amount'                => $this->amount,
            'image'                 => $this->image,
            'reason'                => $this->reason,
            'status'                => $this->status,
        ];
    }
}

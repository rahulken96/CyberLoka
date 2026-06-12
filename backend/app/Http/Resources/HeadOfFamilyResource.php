<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeadOfFamilyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'family_code'    => $this->family_code,
            'user'           => new UserResource($this->user),
            'date_of_birth'  => $this->date_of_birth,
            'image'          => $this->image,
            'occupation'     => $this->occupation,
            'nik'            => $this->nik,
            'gender'         => $this->gender,
            'martial_status' => $this->martial_status,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'family_member_code'    => $this->family_member_code,
            'head_of_family'        => new HeadOfFamilyResource($this->whenLoaded('headOfFamily')),
            'user'                  => new UserResource($this->whenLoaded('user')),
            'date_of_birth'         => $this->date_of_birth,
            'image'                 => $this->image,
            'occupation'            => $this->occupation,
            'nik'                   => $this->nik,
            'gender'                => $this->gender,
            'martial_status'        => $this->martial_status,
            'relation'              => $this->relation,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{

    public function __construct($resource)
    {
        return parent::__construct($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                'id'  => $this->resource->id,
                'name' => $this->resource->name,
                'image' => $this->resource->invitee?->profile_photo_path,
                'email' => $this->resource->invitee?->email,
                'phone' => $this->resource->invitee?->phone,
            ];
    }
}

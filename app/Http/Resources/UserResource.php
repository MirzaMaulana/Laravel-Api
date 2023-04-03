<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'tanggal_lahir' => $this->tanggal_lahir,
            'role' => $this->role,
            'status' => $this->status,
            'jenis_kelamin' => $this->jenis_kelamin,
            'alamat' => $this->alamat,
            'image' => $this->image,
        ];
    }
}

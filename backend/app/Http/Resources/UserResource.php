<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'student_code' => $this->student_code,
            'status' => $this->status,
            'role' => new RoleResource($this->whenLoaded('role')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

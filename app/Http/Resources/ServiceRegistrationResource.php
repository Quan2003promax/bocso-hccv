<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'queue_number' => $this->queue_number,
            'full_name' => $this->full_name,
            'birth_year' => $this->birth_year,
            'identity_number' => $this->identity_number,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'notes' => $this->notes,
            'department' => [
                'id' => $this->department->id,
                'name' => $this->department->name,
                'description' => $this->department->description
            ],
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d H:i:s')
        ];
    }
}

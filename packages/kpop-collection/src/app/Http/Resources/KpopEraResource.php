<?php
namespace HafizRuslan\KpopCollection\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KpopEraResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'versions' => KpopEraVersionResource::collection($this->whenLoaded('versions')),
            'project_id' => $this->project_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

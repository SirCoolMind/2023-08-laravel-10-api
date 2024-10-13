<?php
namespace HafizRuslan\KpopCollection\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KpopItemResource extends JsonResource
{
    public function toArray($request)
    {

        $photocardImage = $this->photocardImage;
        $imageData = [];
        if($photocardImage) {
            $imageData[] = [
                'source' => $photocardImage->retrievePath(),
                'filename' => $photocardImage->original_filename,
                'is_available' => true,
            ];

        }

        return [
            'id' => $this->id,
            'photocard_image' => $imageData,
            'artist_name' => $this->artist_name,
            'era_name' => $this->era_name,
            'version_name' => $this->version_name,
            'kpop_era_id' => new KpopEraResource($this->whenLoaded('era')),
            'kpop_era_version_id' => new KpopEraVersionResource($this->whenLoaded('version')),
            'comment' => $this->comment,
            'bought_price' => $this->bought_price,
            'bought_place' => $this->bought_place,
            'bought_comment' => $this->bought_comment,
            'user_id' => $this->user_id,
            'project_id' => $this->project_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

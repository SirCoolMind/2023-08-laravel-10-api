<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSettingAccountResource extends JsonResource
{
    public function toArray($request)
    {
        $imageData = [];
        foreach ($this->profileImages as $file) {
            $imageData[] = [
                'id'           => $file->id,
                'source'       => $file->retrievePath(),
                'filename'     => $file->original_filename,
                'is_available' => true,
            ];
        }

        return [
            'id'             => $this->id,
            'profile_image' => $imageData,
            'email'          => $this->email,
            'name'           => $this->name,
        ];
    }
}

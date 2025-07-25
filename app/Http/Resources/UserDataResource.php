<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDataResource extends JsonResource
{
    public function toArray($request)
    {
        $imageData = [];
        $avatar = null;
        foreach ($this->profileImages as $file) {
            $avatar = $file->retrievePath();
        }

        return [
            'id'             => $this->id,
            'profile_image'  => $imageData,
            'email'          => $this->email,
            'name'           => $this->name,
            'fullName'       => $this->name,
            'avatar'         => $avatar,
            'role'           => 'basic',
            'abilityRules'   => [
                [
                    'action'  => 'manage',
                    'subject' => 'all',
                ],
            ],
        ];
    }
}

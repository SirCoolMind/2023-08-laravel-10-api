<?php

namespace HafizRuslan\Finance\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoneyAccountResource extends JsonResource
{
    public function toArray($request)
    {
        if (\Illuminate\Support\Str::contains($request->path(), 'lookup/')) {
            return $this->lookup($request);
        }

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'user_id'        => $this->user_id,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }

    public function lookup($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }
}

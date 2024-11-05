<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoneyTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'amount'           => $this->amount,
            'transaction_date' => $this->transaction_date?->toIso8601String(),
            'description'      => $this->description,
            'category'         => $this->category,
            'sub_category'     => $this->sub_category,
            'created_at'       => $this->created_at->format('d/m/Y'),
        ];
    }
}

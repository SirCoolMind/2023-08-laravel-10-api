<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'transaction_type_id' => $this->transaction_type_id,
            'spent_money' => $this->spent_money,
            'spent_date' => $this->spent_date,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->format('d/m/Y') ,
        ];
    }
}

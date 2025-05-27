<?php

namespace HafizRuslan\Finance\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoneyTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transactionImageData = [];
        foreach ($this->transactionImages as $file) {
            $transactionImageData[] = [
                'id'           => $file->id,
                'source'       => $file->retrievePath(),
                'filename'     => $file->original_filename,
                'is_available' => true,
            ];
        }

        return [
            'id'                 => $this->id,
            'amount'             => number_format($this->amount / 100, 2, '.', ''),
            'date'               => $this->getTransactionDateInYmd(),
            'transaction_date'   => $this->transaction_date,
            'description'        => $this->description,
            'source_transaction' => new MoneyTransactionResource($this->whenLoaded('sourceTransaction')),
            'target_transaction' => new MoneyTransactionResource($this->whenLoaded('targetTransaction')),
            'source_account'     => new MoneyAccountResource($this->whenLoaded('sourceMoneyAccount')),
            'target_account'     => new MoneyAccountResource($this->whenLoaded('targetMoneyAccount')),
            'created_at'         => $this->created_at->format('d/m/Y'),
            'transaction_images' => $transactionImageData,
        ];
    }
}

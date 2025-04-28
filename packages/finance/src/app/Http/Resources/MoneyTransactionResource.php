<?php

namespace HafizRuslan\Finance\app\Http\Resources;

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
            'id'                => $this->id,
            'amount'            => number_format($this->amount / 100, 2, '.', ''),   
            'date'              => $this->getTransactionDateInYmd(),
            'transaction_date'  => $this->transaction_date,
            'description'       => $this->description,
            'category'          => $this->category,
            'sub_category'      => $this->sub_category,
            'money_category'    => new MoneyCategoryResource($this->whenLoaded('moneyCategory')),
            'money_subcategory' => new MoneySubCategoryResource($this->whenLoaded('moneySubCategory')),
            'money_account'     => new MoneyAccountResource($this->whenLoaded('moneyAccount')),
            'type'              => [
                'id'   => $this->type?->value,
                'name' => $this->type?->label(),
            ],
            'created_at'         => $this->created_at->format('d/m/Y'),
            'transaction_images' => $transactionImageData,
        ];
    }
}

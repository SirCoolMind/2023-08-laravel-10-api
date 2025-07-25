<?php

namespace HafizRuslan\Finance\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoneyTransactionV2Resource extends JsonResource
{
    /**
     * Simplified version to reduce data sent.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'amount'        => number_format($this->amount / 100, 2, '.', ''),
            'date'          => $this->getTransactionDateInYmd(),
            'description'   => $this->description,
            'type'          => [
                'id'   => $this->type?->value,
                'name' => $this->type?->label(),
            ],
            'money_account' => $this->whenLoaded('moneyAccount', function () {
                return $this->moneyAccount->only(['name']);
            }),
            'money_category' => $this->whenLoaded('moneyCategory', function () {
                return $this->moneyCategory?->only(['name']);
            }),
            'money_subcategory' => $this->whenLoaded('moneySubCategory', function () {
                return $this->moneySubCategory?->only(['name']);
            }),
        ];
    }
}

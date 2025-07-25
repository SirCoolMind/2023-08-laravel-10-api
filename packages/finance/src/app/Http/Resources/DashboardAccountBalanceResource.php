<?php

namespace HafizRuslan\Finance\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardAccountBalanceResource extends JsonResource
{
    /**
     * Simplified version to reduce data sent.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'    => $this->moneyAccount?->name,
            'balance' => number_format($this->balance / 100, 2, '.', ''),
        ];
    }
}

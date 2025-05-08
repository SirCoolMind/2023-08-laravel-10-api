<?php

namespace HafizRuslan\Finance\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyAccount extends Model
{
    use HasFactory;

    public function clearUserCache(): void
    {
        \Cache::forget("money_accounts_" . $this->user_id);
    }

    protected static function booted()
    {
        static::saved(function ($account) {
            $account->clearUserCache();
        });

        static::deleted(function ($account) {
            $account->clearUserCache();
        });
    }
}

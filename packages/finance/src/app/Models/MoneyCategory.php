<?php

namespace HafizRuslan\Finance\app\Models;

use HafizRuslan\Finance\app\Enums\FinanceTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyCategory extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => FinanceTypeEnum::class,
    ];

    public function subCategory()
    {
        return $this->hasMany(MoneySubCategory::class, 'money_category_id', 'id');
    }

    public function clearUserCache(): void
    {
        \App\Helpers\CacheTracker::clearTracked("finance_keys_{$this->user_id}");
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

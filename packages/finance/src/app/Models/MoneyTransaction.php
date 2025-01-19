<?php

namespace HafizRuslan\Finance\app\Models;

use HafizRuslan\Finance\app\Enums\FinanceTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'transaction_date' => 'datetime',
        'type'             => FinanceTypeEnum::class,
    ];

    // Accessor for transaction_date
    public function getTransactionDateAttribute($value)
    {
        // Convert to UTC and then to ISO 8601 format
        return \Carbon\Carbon::parse($value)->utc()->toISOString();  // 'Z' will be appended
    }

    // Method for date in Y-m-d
    public function getTransactionDateInYmd()
    {
        return \Carbon\Carbon::parse($this->attributes['transaction_date'])
            ->utc()
            ->format('Y-m-d');
    }

    public function moneyCategory()
    {
        return $this->hasOne(MoneyCategory::class, 'id', 'money_category_id');
    }

    public function moneySubCategory()
    {
        return $this->hasOne(MoneySubCategory::class, 'id', 'money_subcategory_id');
    }

    /**
     * LIST OF CATEGORY ENUM THINGY.
     *
     * UTILITIES
     * FOOD
     * ENTERTAINMENT
     * TRANSPORTATION
     * FAMILY
     */
}

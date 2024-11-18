<?php

namespace HafizRuslan\Finance\app\Models;

use HafizRuslan\Finance\app\Enums\FinanceCategoryEnum;
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
        // 'category'         => FinanceCategoryEnum::class,
    ];

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

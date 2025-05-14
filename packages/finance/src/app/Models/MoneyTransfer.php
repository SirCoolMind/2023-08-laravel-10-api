<?php

namespace HafizRuslan\Finance\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyTransfer extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'transaction_date' => 'datetime',
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
            // ->utc() // no need utc because not enough data for front end to declare utc or not
            ->format('Y-m-d');
    }

    public function sourceMoneyAccount()
    {
        return $this->hasOne(MoneyAccount::class, 'id', 'source_account_id');
    }

    public function targetMoneyAccount()
    {
        return $this->hasOne(MoneyAccount::class, 'id', 'target_account_id');
    }

    public function sourceTransaction()
    {
        return $this->hasOne(MoneyTransaction::class)->where('type', 'EXPENSE');
    }

    public function targetTransaction()
    {
        return $this->hasOne(MoneyTransaction::class)->where('type', 'INCOME');
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

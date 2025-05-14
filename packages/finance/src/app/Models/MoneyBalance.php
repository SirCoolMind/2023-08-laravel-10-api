<?php

namespace HafizRuslan\Finance\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyBalance extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'transaction_date' => 'date',
    ];

    protected $fillable = [
        'money_account_id',
        'transaction_date',
        'balance',
        'user_id',
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

    public function moneyAccount()
    {
        return $this->hasOne(MoneyAccount::class, 'id', 'money_account_id');
    }
}

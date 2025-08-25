<?php

namespace HafizRuslan\Finance\app\Models;

use HafizRuslan\Finance\app\Enums\FinanceTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SirCoolMind\UploadedFiles\app\Models\UploadedFile;

class MoneyTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    const FileTypeTransactionImages = 'transaction_images';

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
            // ->utc() // no need utc because not enough data for front end to declare utc or not
            ->format('Y-m-d');
    }

    public function transactionImages()
    {
        return $this->morphMany(UploadedFile::class, 'model')
            ->where('type', self::FileTypeTransactionImages);
    }

    public function moneyCategory()
    {
        return $this->hasOne(MoneyCategory::class, 'id', 'money_category_id');
    }

    public function moneySubCategory()
    {
        return $this->hasOne(MoneySubCategory::class, 'id', 'money_subcategory_id');
    }

    public function moneyAccount()
    {
        return $this->hasOne(MoneyAccount::class, 'id', 'money_account_id');
    }

    public function transfer()
    {
        return $this->belongsTo(MoneyTransfer::class, 'money_transfer_id');
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

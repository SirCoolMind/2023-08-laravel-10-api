<?php

namespace HafizRuslan\Finance\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyTransactionExcelBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_no',
        'user_id',
        'transaction_date',
        'amount',
        'description',
        'type',
        'money_account_name',
        'money_category_name',
        'money_subcategory_name',
        'is_valid',
        'error_message',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'is_valid' => 'boolean',
        'amount' => 'decimal:2',
    ];
}

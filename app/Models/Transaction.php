<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'transaction_type_id',
        'spent_money',
        'spent_date',
        'remarks',
    ];

    // Attribute : spent_date
    // default input format : m/d/Y h:i A
    public function setSpentDateAttribute($value)
    {
        $this->attributes['spent_date'] = Carbon::createFromFormat(config('app.datetime_format'), $value)->format(config('app.mysql_datetime_format'));
    }

    public function getSpentDateAttribute($value)
    {
        return Carbon::parse($value)->format(config('app.datetime_format'));
    }

    // Relations
    public function transactionType() {
        return $this->belongsTo(\App\Models\TransactionType::class, 'transaction_type_id', 'id');
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $category_id
 * @property float $spent_money
 * @property string $spent_date
 * @property string $remarks
 * @property string $created_at
 * @property string $updated_at
 */
class Transaction extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['category_id', 'spent_money', 'spent_date', 'remarks', 'created_at', 'updated_at'];

    public function setSpentDateAttribute($value)
    {
        $this->attributes['spent_date'] = Carbon::createFromFormat(config('app.datetime_format'), $value)->format(config('app.mysql_datetime_format'));
    }

    public function getSpentDateAttribute($value)
    {
        return Carbon::parse($value)->format(config('app.datetime_format'));
    }
}

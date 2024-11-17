<?php

namespace HafizRuslan\Finance\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneySubCategory extends Model
{
    use HasFactory;

    protected $table = 'money_subcategories';

    public function category()
    {
        return $this->hasOne(MoneyCategory::class, 'id', 'money_category_id');
    }
}

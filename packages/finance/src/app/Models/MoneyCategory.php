<?php

namespace HafizRuslan\Finance\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyCategory extends Model
{
    use HasFactory;

    public function subCategory()
    {
        return $this->hasMany(MoneySubCategory::class, 'money_category_id', 'id');
    }
}

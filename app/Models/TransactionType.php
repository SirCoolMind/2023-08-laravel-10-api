<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'name' => '',
        'description' => '',
        'is_active' => true,
    ];

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_active',
    ];
}

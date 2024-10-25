<?php

namespace HafizRuslan\KpopCollection\app\Models;

use Illuminate\Database\Eloquent\Model;

class KpopEraVersion extends Model
{
    protected $table = 'kpop_eras_versions';

    public function era()
    {
        return $this->hasOne(KpopEra::class, 'id', 'kpop_era_id');
    }
}

<?php
namespace HafizRuslan\KpopCollection\app\Models;

use Illuminate\Database\Eloquent\Model;
use HafizRuslan\KpopCollection\app\Models\KpopEraVersion;

class KpopEra extends Model
{
    protected $table = 'kpop_eras';

    /*relationship */
    public function versions(){
        return $this->hasMany(KpopEraVersion::class, 'kpop_era_id', 'id');
    }
}

<?php
namespace HafizRuslan\KpopCollection\app\Models;

use Illuminate\Database\Eloquent\Model;
use HafizRuslan\KpopCollection\app\Models\KpopEraVersion;
use HafizRuslan\KpopCollection\app\Models\KpopEra;

class KpopItem extends Model
{
    protected $table = 'kpop_items';

    /*relationship */
    public function era(){
        return $this->hasOne(KpopEra::class, 'id', 'kpop_era_id');
    }

    public function versions(){
        return $this->hasOne(KpopEraVersion::class, 'id', 'kpop_eras_version_id');
    }
}

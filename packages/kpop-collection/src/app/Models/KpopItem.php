<?php
namespace HafizRuslan\KpopCollection\app\Models;

use Illuminate\Database\Eloquent\Model;
use HafizRuslan\KpopCollection\app\Models\KpopEraVersion;
use HafizRuslan\KpopCollection\app\Models\KpopEra;
use SirCoolMind\UploadedFiles\app\Models\UploadedFile;

class KpopItem extends Model
{
    protected $table = 'kpop_items';

    public $fileTypePhotocardImage = "photocard_image";

    /*relationship */
    public function era(){
        return $this->hasOne(KpopEra::class, 'id', 'kpop_era_id');
    }

    public function versions(){
        return $this->hasOne(KpopEraVersion::class, 'id', 'kpop_eras_version_id');
    }

    public function photocardImage(){
        return $this->morphOne(UploadedFile::class, 'model')
            ->where('type', $this->fileTypePhotocardImage);
    }
}

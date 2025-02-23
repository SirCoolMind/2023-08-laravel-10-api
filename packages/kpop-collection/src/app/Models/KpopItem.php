<?php

namespace HafizRuslan\KpopCollection\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SirCoolMind\UploadedFiles\app\Models\UploadedFile;

class KpopItem extends Model
{
    use SoftDeletes;
    protected $table = 'kpop_items';

    const FileTypePhotocardImage = 'photocard_image';

    /*relationship */
    public function era()
    {
        return $this->hasOne(KpopEra::class, 'id', 'kpop_era_id');
    }

    public function version()
    {
        return $this->hasOne(KpopEraVersion::class, 'id', 'kpop_era_version_id');
    }

    public function photocardImages()
    {
        return $this->morphMany(UploadedFile::class, 'model')
            ->where('type', self::FileTypePhotocardImage);
    }
}

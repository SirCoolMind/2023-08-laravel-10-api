<?php

namespace SirCoolMind\UploadedFiles\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadedFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'filename',
        'original_filename',
        'type',
        'path',
        'size',
        'extension',
    ];

    public function source()
    {
        return $this->morphTo();
    }

    public function retrievePath()
    {
        // Generate a signed URL valid for 1 hour (3600 seconds)
        return \URL::signedRoute('files.download', ['id' => $this->id], now()->addHour());
    }

    // TODO:: create a helper class for store/retrieve/delete
    public static function store($model = null, $type = null, $files = null)
    {
        if(!$files || !$model) {
            \Log::error("UploadedFile::store() || Files or model is missing");
            return;
        }

        if (! is_array($files)) {
            $files = array($files);
        }

        foreach ($files as $file) {
            UploadedFile::handleFileUpload($model, $type, $file);
        }

    }

    private static function handleFileUpload($model = null, $type = null, $file = null, )
    {
        if(!$file) {
            \Log::error("UploadedFile::handleFileUpload() || File is missing");
            return;
        }

        if(!$model) {
            \Log::error("UploadedFile::handleFileUpload() || Model is missing");
            return;
        }

        try {

            \DB::beginTransaction();

            // Store the file inside server
            $modelType = get_class($model);
            $modelId = $model->id;

            $pathName = $modelType."/".$modelId;
            $encryptedName = \Str::random(40) . '.' . $file->getClientOriginalExtension();  // Encrypting filename
            $filePath = $file->storeAs($pathName, $encryptedName, 'public');

            $upload = new UploadedFile;

            $upload->model_type = $modelType;
            $upload->model_id = $modelId;
            $upload->type = $type;

            $upload->filename = $encryptedName;
            $upload->original_filename = $file->getClientOriginalName();
            $upload->path = $filePath;
            $upload->size = $file->getSize();
            $upload->extension = strtolower($file->getClientOriginalExtension());

            $upload->save();

            \DB::commit();

        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error("UploadedFile::handleFileUpload() || error saving");
            \Log::debug($th->getMessage());
        }

    }
}

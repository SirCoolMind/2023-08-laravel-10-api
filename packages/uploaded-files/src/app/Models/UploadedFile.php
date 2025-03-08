<?php

namespace SirCoolMind\UploadedFiles\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intervention\Image\Drivers\Gd\Encoders\JpegEncoder;
use Intervention\Image\Drivers\Gd\Encoders\PngEncoder;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class UploadedFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'filename',
        'original_filename',
        'safe_filename',
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
        return \URL::signedRoute('files.download', ['id' => $this->id, 'filename' => $this->safe_filename], now()->addHour());
    }

    // TODO:: create a helper class for store/retrieve/delete
    public static function store($model = null, $type = null, $files = null, $imageQualityCompress = 75)
    {
        if (!$files || !$model) {
            \Log::error('UploadedFile::store() || Files or model is missing');

            return;
        }

        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            UploadedFile::handleFileUpload($model, $type, $file, $imageQualityCompress);
        }
    }

    private static function handleFileUpload($model = null, $type = null, $file = null, $imageQualityCompress = 75)
    {
        if (!$file) {
            \Log::error('UploadedFile::handleFileUpload() || File is missing');

            return;
        }

        if (!$model) {
            \Log::error('UploadedFile::handleFileUpload() || Model is missing');

            return;
        }

        try {
            \DB::beginTransaction();

            // Store the file inside server
            $modelType = get_class($model);
            $modelId = $model->id;

            $pathName = $modelType.'/'.$modelId;
            $encryptedName = \Str::random(40).'.'.$file->getClientOriginalExtension();  // Encrypting filename
            $filePath = $pathName.'/'.$encryptedName; // Path where the file will be saved

            // Check if the file is an image
            if (str_starts_with($file->getMimeType(), 'image/')) {
                // Resize & compress image
                $image = Image::read($file);

                // Choose the correct encoder based on the file extension
                $extension = strtolower($file->getClientOriginalExtension());
                $encoder = match ($extension) {
                    'jpg', 'jpeg' => new JpegEncoder($imageQualityCompress),  // JPEG compression
                    'png'   => new PngEncoder(),
                    'webp'  => new WebpEncoder($imageQualityCompress),  // WebP compression
                    default => new JpegEncoder($imageQualityCompress), // Default to JPEG
                };

                // Encode and store the image
                \Storage::disk('public')->put($filePath, $image->encode($encoder));
            } else {
                // Store non-image files normally
                $filePath = $file->storeAs($pathName, $encryptedName, 'public');
            }

            $upload = new UploadedFile();

            $upload->model_type = $modelType;
            $upload->model_id = $modelId;
            $upload->type = $type;

            $upload->filename = $encryptedName;
            $upload->original_filename = $file->getClientOriginalName();
            $upload->safe_filename = $upload::makeUrlSafe($file->getClientOriginalName());
            $upload->path = $filePath;
            $upload->size = $file->getSize();
            $upload->extension = strtolower($file->getClientOriginalExtension());

            $upload->save();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error('UploadedFile::handleFileUpload() || error saving');
            \Log::debug($th->getMessage());

            throw new \Exception('Error uploading file');
        }
    }

    private static function makeUrlSafe($string)
    {
        // Convert UTF-8 characters to ASCII (fallback to original if conversion fails)
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        if ($transliterated === false) {
            $transliterated = $string;
        }

        // Remove special characters except alphanumerics, spaces, and hyphens
        $cleaned = preg_replace('/[^a-zA-Z0-9\s-]/u', '', $transliterated);

        // Replace multiple spaces or hyphens with a single hyphen
        $cleaned = preg_replace('/[\s-]+/', '-', trim($cleaned, " \t\n\r\0\x0B-"));

        // Convert to lowercase
        return strtolower($cleaned);
    }
}

<?php

namespace SirCoolMind\UploadedFiles\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Intervention\Image\Drivers\Gd\Encoders\JpegEncoder;
use Intervention\Image\Drivers\Gd\Encoders\PngEncoder;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;

class UploadedFile extends Model
{
    use SoftDeletes;

    const FILE_SIZE_LIMIT = '5242880'; //5MB

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

    /**
     * Store uploaded file(s) and associate them with a given model.
     *
     * This method accepts a single file or an array of files, optionally compresses image quality,
     * and delegates file handling to the handleFileUpload() method.
     *
     * If the model or files are missing, the method logs an error and returns early.
     *
     * @param Model|null                                         $model The model to associate the uploaded file(s) with.
     * @param string|null                                        $type  A type or category identifier for the file(s), e.g. image, document, etc.
     * @param \Illuminate\Http\UploadedFile|UploadedFile[]|null  $files A single UploadedFile instance or an array of them.
     * @param array|null                                         $resizeTarget Resize image size ([120,120])
     * @param int                                                $imageQualityCompress Image compression quality (1–100) for images. Default is 75.
     *
     * @return void
     */
    public static function store(?Model $model = null, ?string $type = null, \Illuminate\Http\UploadedFile|array|null $files = null, ?array $resizeTarget = null, int $imageQualityCompress = 75)
    {
        if (!$files || !$model) {
            \Log::error('UploadedFile::store() || Files or model is missing');
            return;
        }

        $resizeTarget = self::validateResizeTarget($resizeTarget);
        $files = is_array($files) ? $files : [$files];
        foreach ($files as $file) {
            // Resize if not null and it is image file
            if ( $resizeTarget && \Str::startsWith($file->getMimeType(), 'image/') ) {
                $file = self::resizeImage($file, $resizeTarget[0], $resizeTarget[1], $imageQualityCompress);
            }

            self::handleFileUpload($model, $type, $file, $imageQualityCompress);

            // Clean up temp file
            if (!is_null($resizeTarget) && file_exists($file->getPathname())) {
                @unlink($file->getPathname());
            }
        }
    }

    /**
     * Sync files based on existingFiles input.
     * If file exists in system but not in request, remove it.
     * If nothing inside request, remove all
     *
     * @param Collection $uploadedFiles Collection of uploaded file models
     * @param array|null $existingFiles Input from the request
     */
    public static function syncFiles(Collection $uploadedFiles, ?array $existingFiles = null): void
    {
        if (empty($existingFiles)) {
            foreach ($uploadedFiles as $uploadedFile) {
                \Storage::disk('public')->delete($uploadedFile->path);
                $uploadedFile->delete();
            }
            return;
        }

        $existingFiles = collect($existingFiles)->filter(function ($image) {
            return $image['is_available'] === 'true';
        });

        foreach ($uploadedFiles as $uploadedFile) {
            $match = $existingFiles->first(function ($image) use ($uploadedFile) {
                return $image['id'] == $uploadedFile->id &&
                       $image['filename'] === $uploadedFile->original_filename;
            });

            if (!$match) {
                self::deleteFile($uploadedFile);
            }
        }
    }

    /**
     * Delete a single uploaded file from the storage disk and database.
     *
     * @param \Illuminate\Database\Eloquent\Model $uploadedFile
     *        An Eloquent model instance representing the uploaded file. Must have a `path` attribute.
     *
     * @return void
     *
     * @throws \Exception If the provided model is invalid or deletion fails.
     */
    public static function deleteFile($uploadedFile)
    {
        if (!$uploadedFile || !isset($uploadedFile->path)) {
            \Log::warning('FileHelper::deleteFile() called with invalid file model.');

            throw new \Exception('called with invalid file model');
        }

        try {
            \Storage::disk('public')->delete($uploadedFile->path);
            $uploadedFile->delete();
        } catch (\Throwable $e) {
            \Log::error('FileHelper::deleteFile() error: ' . $e->getMessage());

            throw new \Exception($e->getMessage());
        }
    }

    private static function handleFileUpload($model = null, $type = null, $file = null, $imageQualityCompress = 75)
    {
        if (!$file) {
            \Log::error('UploadedFile::handleFileUpload() || File is missing');

            throw new \Exception("File is missing");
        }

        if ($file->getSize() >= self::FILE_SIZE_LIMIT) {
            \Log::error('UploadedFile::handleFileUpload() || File is larger than 5MB');

            throw new \Exception("File is larger than 5MB");
        }

        if (!$model) {
            \Log::error('UploadedFile::handleFileUpload() || Model is missing');

            throw new \Exception("Model is missing");
        }

        try {
            \DB::beginTransaction();

            // Store the file inside server
            $modelType = get_class($model);
            $modelId = $model->id;

            $pathName = $modelType.'/'.$modelId;
            $encryptedName = \Str::random(40).'.'.$file->getClientOriginalExtension();  // Encrypting filename
            $filePath = $pathName.'/'.$encryptedName; // Path where the file will be saved
            $filePath = str_replace('\\', '/', $filePath); // cleanup any backslash to slash

            // Check if the file is an image
            if (\Str::startsWith($file->getMimeType(), 'image/')) {
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
            $upload->extension = strtolower($file->getClientOriginalExtension());
            $safeFilename = $upload::makeUrlSafe(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $upload->safe_filename = $safeFilename.".".$upload->extension;
            $upload->path = $filePath;
            $upload->size = $file->getSize();

            $upload->save();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error('UploadedFile::handleFileUpload() || error saving');
            \Log::error($th->getMessage());

            throw new \Exception('Error uploading file');
        }
    }

    private static function resizeImage(\Illuminate\Http\UploadedFile $file, int $width, int $height): \Illuminate\Http\UploadedFile
    {
        $image = ImageManager::imagick()
            ->read($file->getPathname())
            ->cover($width, $height);

        $tempPath = sys_get_temp_dir() . '/' . \Str::uuid() . '.' . $file->getClientOriginalExtension();
        $image->save($tempPath, $quality = 100);

        return new \Illuminate\Http\UploadedFile(
            $tempPath,
            $file->getClientOriginalName(),
            $file->getClientMimeType(),
            0,
            true
        );
    }

    protected static function validateResizeTarget(?array $targetSize): ?array
    {
        if (
            is_array($targetSize) &&
            count($targetSize) === 2 &&
            is_int($targetSize[0]) &&
            is_int($targetSize[1]) &&
            $targetSize[0] > 0 &&
            $targetSize[1] > 0
        ) {
            return $targetSize;
        }

        // Invalid size = skip resize, no error thrown
        return null;
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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserSettingRequest;
use App\Http\Resources\UserSettingAccountResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use SirCoolMind\UploadedFiles\app\Models\UploadedFile;

class UserSettingAccountController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $user = \Auth::user();

        return response()->json([
            'message' => __('Record successfuly retrieved.'),
            'data'    => new UserSettingAccountResource($user),
        ]);
    }

    public function update(UpdateUserSettingRequest $request, $id)
    {
        $request->validated($request->all());

        $user = \Auth::user();
        if ($user->id != $id) {
            return $this->error('', 'Unauthorized', 401);
        }

        try {
            \DB::beginTransaction();

            $user->update([
                'name'     => $request->name,
                'email'    => $request->email,
            ]);

            $existingImages = $request->input('profile_image');
            // If existingImages is empty, delete all files related to the model
            if (empty($existingImages)) {
                foreach ($user->profileImages as $uploadedFile) {
                    Storage::disk('public')->delete($uploadedFile->path);
                    $uploadedFile->delete();
                }
            } else {
                // If existingImages is not empty, check which files to delete
                foreach ($user->profileImages as $uploadedFile) {
                    $shouldDelete = true;
                    // \Log::debug("file");
                    // \Log::debug($uploadedFile);
                    foreach ($existingImages as $image) {
                        // \Log::debug($image);
                        if ($image['id'] == $uploadedFile->id
                            && $image['filename'] == $uploadedFile->original_filename
                            && $image['is_available'] == 'true'
                        ) {
                            $shouldDelete = false;
                            break;
                        }
                    }
    
                    if ($shouldDelete) {
                        Storage::disk('public')->delete($uploadedFile->path);
                        $uploadedFile->delete();
                    }
                }
            }
    
            if ($request->hasFile('profile_image_upload')) {
                //Resize profile image to 120x120
                $file = $request->file('profile_image_upload.0');
                $filePath = $file->getPathname();
                $image = ImageManager::imagick()->read($filePath)->cover(120, 120);
                
                $tempPath = sys_get_temp_dir() . '/' . \Str::uuid() . '.' . $file->getClientOriginalExtension();
                $image->save($tempPath, 100);

                $resizedFile = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    $file->getClientOriginalName(),
                    $file->getClientMimeType(),
                    0, // Error status (0 means no error)
                    true // Test mode (prevents moving the file)
                );

                UploadedFile::store($user, User::FileTypeProfileImage, $resizedFile, $imageQualityCompress = 90);

                // 🔥 Delete the temp file after storing
                unlink($tempPath);
            }

            $user->refresh();
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th);

            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully updated.'),
            'data'    => new UserSettingAccountResource($user),
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'message' => 'You have successfully been logged out.',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserSettingRequest;
use App\Http\Resources\UserSettingAccountResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
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

            UploadedFile::syncFiles($user->profileImages, $request->input('profile_image'));

            if ($request->hasFile('profile_image_upload')) {
                UploadedFile::store($user, User::FileTypeProfileImage, $resizedFile, $targetSize = [120, 120], $imageQualityCompress = 90);
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

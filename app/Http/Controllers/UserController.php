<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use HttpResponses;

    public function index(Request $request){

        // * sort
        $sortBy = '';
        switch ($request->input('sort_by')) {
            case 'name':
                $sortBy = 'name';
                break;
            case 'email':
                $sortBy = 'email';
                break;
            default:
                $sortBy = 'created_at';
                break;
        }
        $sortOrder = $request->input('sort_order') == "desc" ? "desc" : "asc";
        $search = $request->input('search');
        // \Log::debug("sortby, sortOrder||". $sortBy." : ".$sortOrder);

        $users = User::query()
                    ->when($search, function($query) use($search){
                        return $query
                            ->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    })
                    ->orderBy($sortBy, $sortOrder)
                    ->paginate($request->input('items_per_page'));
        if($users){
            return \App\Http\Resources\UserResource::collection($users);
        }
        return $this->error('','No data found',404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        \DB::beginTransaction();
        try {
            $user = new User;
            $user = $this->setUser($request, $user);

        } catch (\Throwable $e) {

            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['user' => UserResource::make($user)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->success(['user' => UserResource::make($user)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        \DB::beginTransaction();
        try {
            $user = $this->setUser($request, $user);

        } catch (\Throwable $e) {

            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['user' => UserResource::make($user)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
        } catch (\Throwable $e) {
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }
        return $this->success('',"Data deleted succesfully");
    }

    /**
     * Set values from request into model (user)
     *
     * @param Request   $request    a Request instance
     * @param User      $user       an User instance
     *
     * @return User     $user   updated User instance
     */
    private function setUser($request, User $user){

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($password = $request->input('password')) {
            $user->password = \Hash::make($password);
        }

        $user->save();
        return $user;
    }
}

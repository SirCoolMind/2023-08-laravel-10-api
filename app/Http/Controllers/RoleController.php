<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\RoleStoreRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use HttpResponses;

    public function index(Request $request){

        // * sort
        $sortBy = '';
        switch ($request->input('sort_by')) {
            case 'name':
                $sortBy = 'name';
                break;
            default:
                $sortBy = 'created_at';
                break;
        }
        $sortOrder = $request->input('sort_order') == "desc" ? "desc" : "asc";
        $search = $request->input('search');
        // \Log::debug("sortby, sortOrder||". $sortBy." : ".$sortOrder);

        $roles = Role::query()
                    ->when($search, function($query) use($search){
                        return $query
                            ->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orderBy($sortBy, $sortOrder)
                    ->paginate($request->input('items_per_page'));
        if($roles){
            return RoleResource::collection($roles);
        }
        return $this->error('','No data found',404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleStoreRequest $request)
    {
        \DB::beginTransaction();
        try {
            $role = new Role;
            $role = $this->setRole($request, $role);

        } catch (\Throwable $e) {

            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['data' => RoleResource::make($role)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return $this->success(['data' => RoleResource::make($role)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        \DB::beginTransaction();
        try {
            $role = $this->setRole($request, $role);

        } catch (\Throwable $e) {

            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['data' => RoleResource::make($role)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            \DB::table('roles')->where('id',$role->id)->delete();
        } catch (\Throwable $e) {
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }
        return $this->success('',"Data deleted succesfully");
    }

    /**
     * Set values from request into model (role)
     *
     * @param Request $request a Request instance
     * @param Role $role an Role instance
     *
     * @return Role $role updated Role instance
     */
    private function setRole($request, Role $role){

        $role->name = $request->input('name');
        $role->save();
        return $role;
    }
}

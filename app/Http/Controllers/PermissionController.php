<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\PermissionStoreRequest;
use App\Http\Requests\Admin\PermissionUpdateRequest;
use App\Http\Resources\PermissionResource;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use HttpResponses;
    protected function getDefaultGuardName(): string { return 'web'; }

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

        $permissions = Permission::query()
                    ->when($search, function($query) use($search){
                        return $query
                            ->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orderBy($sortBy, $sortOrder)
                    ->paginate($request->input('items_per_page'));
        if($permissions){
            return PermissionResource::collection($permissions);
        }
        return $this->error('','No data found',404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionStoreRequest $request)
    {
        \DB::beginTransaction();
        try {
            $permission = new Permission;
            $permission = $this->setPermission($request, $permission);

        } catch (\Throwable $e) {

            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['data' => PermissionResource::make($permission)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        return $this->success(['data' => PermissionResource::make($permission)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        \DB::beginTransaction();
        try {
            $permission = $this->setPermission($request, $permission);

        } catch (\Throwable $e) {

            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        return $this->success(['data' => PermissionResource::make($permission)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        try {
            \DB::table('permissions')->where('id',$permission->id)->delete();
        } catch (\Throwable $e) {
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }
        return $this->success('',"Data deleted succesfully");
    }

    /**
     * Set values from request into model (permission)
     *
     * @param Request $request a Request instance
     * @param Permission $permission an Permission instance
     *
     * @return Permission $permission updated Permission instance
     */
    private function setPermission($request, Permission $permission){

        $permission->name = $request->input('name');
        $permission->guard_name = $this->getDefaultGuardName();
        $permission->save();
        return $permission;
    }
}

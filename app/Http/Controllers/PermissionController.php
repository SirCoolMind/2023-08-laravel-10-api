<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\PermissionMultipleUpdateRequest;
use App\Http\Requests\Admin\PermissionStoreRequest;
use App\Http\Requests\Admin\PermissionUpdateRequest;
use App\Http\Resources\PermissionMultipleResource;
use App\Http\Resources\PermissionResource;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
            return $this->success(PermissionResource::collection($permissions)->resource);
        }
        return $this->error('','No data found',404);
    }

    public function indexMultiple(Request $request){

        // * sort
        $sortBy = '';
        switch ($request->input('sort_by')) {
            case 'name':
                $sortBy = 'name';
                break;
            default:
                $sortBy = 'module';
                break;
        }
        $sortOrder = $request->input('sort_order') == "desc" ? "desc" : "asc";
        $search = $request->input('search');
        // \Log::debug("sortby, sortOrder||". $sortBy." : ".$sortOrder);

         // Your raw SQL query to group by 'category' and concatenate 'action' values
        $query = "
            SELECT module, GROUP_CONCAT(action) as actions
            FROM permissions
            GROUP BY module
        ";

        // Execute the raw query
        $groupedResults = \DB::select($query);

        // Paginate the raw results manually
        $perPage = $request->input('items_per_page');
        $currentPage = $request->input('page');
        $total = count($groupedResults);
        $start = ($currentPage - 1) * $perPage;
        $slicedResults = array_slice($groupedResults, $start, $perPage);

        $paginatedResults = new LengthAwarePaginator(
            $slicedResults,
            $total,
            $perPage,
            $currentPage,
        );

        if($total != 0){
            // return $paginatedResults;
            return $this->success(PermissionMultipleResource::collection($paginatedResults)->resource);
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
     * Display the specified resource by module.
     */
    public function showMultiple($module = null)
    {
        $actions = Permission::where('module', $module)->get()?->pluck('action')?->toArray() ?? [];
        $data = [
            'module' => $module,
            'actions' => $actions,
        ];
        return $this->success(['data' => $data]);
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
     * Update multiple permission
    */
    public function updateMultiple(PermissionMultipleUpdateRequest $request)
    {
        \DB::beginTransaction();
        try {
            $this->setMultiplePermissions($request);

        } catch (\Throwable $e) {

            \DB::rollback();
            return $this->error(['detail' => $e->getMessage()],'', 422);
        }

        \DB::commit();
        $module = $request->input('module');
        $actions = Permission::where('module', $module)->get()?->pluck('action')?->toArray() ?? [];
        $data = [
            'module' => $module,
            'actions' => $actions,
        ];
        return $this->success(['data' => $data]);
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

        $permission->name = $name = $request->input('name');
        $pieces = explode(".", $name);
        $permission->module = $pieces[0];
        $permission->action = $pieces[1] ?? $pieces[0];
        $permission->guard_name = $this->getDefaultGuardName();
        $permission->save();

        return $permission;
    }

    /**
     * Set multiple permission and delete unused (Permission Page)
     *
     * @param Request $request a Request instance
     *
     */
    private function setMultiplePermissions($request){

        $module = $request->input('module');
        $actions = $request->input('actions');

        $keepThisId = [];
        $permissions = Permission::where('module', $module)->get();

        foreach($actions as $action){

            $permission = $permissions->firstWhere('action', $action);
            if (!$permission) {

                $permissionData = [
                    'module' => $module,
                    'action' => $action,
                    'name' => $module.'.'.$action,
                    'guard_name' => $this->getDefaultGuardName(),
                ];

                $permission = Permission::create($permissionData);
            }
            $keepThisId[] = $permission->id;
        }
        $permissionDelete = Permission::where('module', $module)->whereNotIn('id',$keepThisId)->delete();
    }
}

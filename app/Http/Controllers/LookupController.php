<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;

class LookupController extends Controller
{
    use HttpResponses;
    /*
    |--------------------------------------------------------------------------
    | Lookup/Retrieve Data Controller
    |--------------------------------------------------------------------------
    |
    | Method of retrieving data can be vary. You can either customize, loop, add new array
    | or just call the existing listing inside controller
    |
    */

    public function transactionType()
    {
        $controller = new \App\Http\Controllers\Api\V1\Transaction\TransactionType\TransactionTypeController;
        return $controller->index();

    }

    public function permissionBasedOnModule()
    {
        $data = [];
        $allPermission = \Spatie\Permission\Models\Permission::orderBy('module')->get();
        $modules = $allPermission->pluck('module')->unique();
        foreach($modules as $module){
            $permissions = $allPermission->where('module',$module);
            $actions = [];
            foreach($permissions as $permission){
                $actions[] = [
                    'id' => $permission->id,
                    'action' => $permission->action,
                ];
            }
            $data[] = [
                'module' => $module,
                'actions' => $actions,
            ];
        }
        return $this->success(['data' => $data]);

    }
}

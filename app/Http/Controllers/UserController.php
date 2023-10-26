<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
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
        \Log::debug("sortby, sortOrder||". $sortBy." : ".$sortOrder);

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
}

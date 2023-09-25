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

    public function allUsers(){

        $allUsers = User::all();

        if($allUsers){
            return $this->success([
                'users' => $allUsers
            ]);

        }
        return $this->error('','No data found',401);


    }
}

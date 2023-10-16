<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LogoutUserController extends Controller
{
    public function logout_user(Request $request){
        unset($_SESSION["email"]);
        unset($_SESSION["name"]);
        unset($_SESSION["surname"]);
        unset($_SESSION["user_id"]);

        return response()->json([
            'message' => 'User logged out successfully'
        ], 201);

    }
}

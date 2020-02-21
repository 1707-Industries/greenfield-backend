<?php

namespace App\Http\Controllers;

use App\Http\Requests\SigninStoreRequest;
use App\Providers\Registered;
use App\User;

class MeController extends Controller
{
    public function index() {
        $user = auth('api')->user();

        return response()->json($user);
    }
}

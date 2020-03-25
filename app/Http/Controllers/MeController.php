<?php

namespace App\Http\Controllers;

use App\Http\Requests\SigninStoreRequest;
use App\Http\Resources\MeResource;
use App\Providers\Registered;
use App\User;

class MeController extends Controller
{
    public function index() {
        $user = auth('api')->user();

        return new MeResource($user);
    }
}

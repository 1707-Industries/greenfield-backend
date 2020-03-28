<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMeRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\MeResource;
use Illuminate\Http\Response;

class PasswordController extends Controller
{
    public function update(UpdatePasswordRequest $request)
    {
        $user = auth('api')->user();

        $user->update($request->only([
            'password'
        ]));

        return response()->json([])->setStatusCode(Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMeRequest;
use App\Http\Resources\MeResource;
use Illuminate\Http\Response;

class MeController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();

        return new MeResource($user);
    }

    public function update(UpdateMeRequest $request)
    {
        $user = auth('api')->user();

        $user->update($request->only([
            'first_name',
            'surname',
            'email'
        ]));

        return response()->json(new MeResource($user))->setStatusCode(Response::HTTP_OK);
    }
}

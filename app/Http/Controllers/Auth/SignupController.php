<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupStoreRequest;
use App\Providers\Registered;
use App\User;

class SignupController extends Controller
{
    public function store(SignupStoreRequest $request) {
        $user = new User($request->only([
            'first_name',
            'surname',
            'email',
            'password'
        ]));

        $user->save();

        event(new Registered($user));

    }
}

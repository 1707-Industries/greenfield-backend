<?php

use App\AppInfo;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\PasswordController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json((new AppInfo)->getInfo());
});

Route::group([], function (Router $router) {
    $router->group(['prefix' => 'auth'], function (Router $router) {
        $router->post('signup', [
            'uses' => SignupController::class . '@store',
            'as' => 'auth.signup.store',
        ]);

        $router->post('forgot-password', [
            'uses' => ForgotPasswordController::class . '@sendResetLinkEmail',
            'as' => 'auth.forgot-password'
        ]);

        $router->post('reset-password', [
           'uses' => ResetPasswordController::class . '@reset',
           'as' => 'auth.reset-password',
        ]);
    });

    $router->group(['prefix' => 'verify-email'], function (Router $router) {
        $router->get('/', [
            'as' => 'verification.show',
            'uses' => VerificationController::class . '@show'
        ]);

        $router->group(['middleware' => 'auth:api'], function (Router $router) {
            $router->post('/', [
                'as' => 'verification.store',
                'uses' => VerificationController::class . '@store'
            ]);

            $router->post('resend-verification', [
                'as' => 'verification.resend',
                'uses' => VerificationController::class . '@resend'
            ]);
        });
    });

    $router->group(['middleware' => ['auth:api',]], function (Router $router) {
        $router->get('me', [
            'uses' => MeController::class . '@index',
            'as' => 'me.index'
        ]);

        $router->group(['middleware' => ['verified']], function (Router $router) {
            $router->patch('me', [
                'uses' => MeController::class . '@update',
                'as' => 'me.update',
            ]);

            $router->put('password', [
                'uses' => PasswordController::class . '@update',
                'as' => 'password.update',
            ]);
        });
    });
});

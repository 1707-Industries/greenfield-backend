<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Reset the given user's password.
     *
     * @param ResetPasswordRequest $request
     * @return RedirectResponse|JsonResponse
     * @throws Exception
     */
    public function reset(ResetPasswordRequest $request)
    {
        $response = $this->broker()->reset(
            $request->only([
                'email',
                'password',
                'token'
            ]),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($response);
    }

    /**
     * Reset the given user's password.
     *
     * @param User $user
     * @param string $password
     * @return void
     */
    protected function resetPassword(User $user, $password)
    {
        $user->password = $password;

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param string $response
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetResponse($response)
    {
        return response()->json([
            'status' => trans($response),
        ]);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param string $response
     * @return RedirectResponse|JsonResponse
     * @throws Exception
     */
    protected function sendResetFailedResponse($response)
    {
        return response()->json([
            'message' => trans($response),
        ])->setStatusCode(422);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Send a reset link to the given user.
     *
     * @param ForgotPasswordRequest $request
     * @return RedirectResponse|JsonResponse
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $response = $this->broker()->sendResetLink($request->only('email'));

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param ForgotPasswordRequest $request
     * @param string $response
     * @return JsonResponse
     */
    protected function sendResetLinkResponse(ForgotPasswordRequest $request, $response)
    {
        return response()->json([
            'status' => trans($response),
        ]);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param ForgotPasswordRequest $request
     * @param string $response
     * @return JsonResponse
     */
    protected function sendResetLinkFailedResponse(ForgotPasswordRequest $request, $response)
    {
        return response()->json([
            'errors' => [
                'email' => [trans($response)],
            ],
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
}

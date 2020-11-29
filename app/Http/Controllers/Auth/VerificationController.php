<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
    }

    public function show(Request $request)
    {
        $queryString = $request->getQueryString();
        return redirect()->to(sprintf('%s/verify-email-address/?%s', config('app.frontend_url'), $queryString));
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request)
    {
        if (!hash_equals((string)$request->get('id'), (string)$request->user()->getKey())) {
            throw new AuthorizationException;
        }

        if (!hash_equals(
            (string)$request->get('hash'),
            sha1($request->user()->getEmailForVerification())
        )) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'verified' => true,
            ]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json([
            'verified' => true,
        ]);
    }

    /**
     * Resend the email verification notification.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resend(Request $request)
    {
        $response = [];

        if ($request->user()->hasVerifiedEmail()) {
            $response['resent'] = false;
        } else {
            $request->user()->sendEmailVerificationNotification();

            $response['resent'] = true;
        }

        return response()->json($response);
    }
}

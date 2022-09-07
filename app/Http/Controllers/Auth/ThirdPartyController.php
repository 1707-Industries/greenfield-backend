<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\ThirdPartyAuthConnection;
use App\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class ThirdPartyController extends Controller
{

    public function redirect($provider)
    {
        $thirdPartyProvider = Socialite::driver($provider);

        if (method_exists($thirdPartyProvider, 'stateless')) {
            $thirdPartyProvider = $thirdPartyProvider->stateless();
        }

        return $thirdPartyProvider->redirect();
    }

    public function callback($provider)
    {
        $providerUser = Socialite::driver($provider);

        if (method_exists($providerUser, 'stateless')) {
            $providerUser = $providerUser->stateless();
        }

        $providerUser = $providerUser->user();

        // Find or create the user
        $thirdPartyAuthConnection = ThirdPartyAuthConnection::firstOrNew([
            'provider' => $provider,
            'provider_id' => $providerUser->id,
        ], [
            'token' => $providerUser->token,
            'refresh_token' => isset($providerUser->refreshToken) ? $providerUser->refreshToken : null,
            'token_secret' => isset($providerUser->tokenSecret) ? $providerUser->tokenSecret : null,
            'expires_in' => isset($providerUser->expiresIn) ? $providerUser->expiresIn : null,
            'oauth_version' => isset($providerUser->tokenSecret) ? 1 : 2,
            'provider_name' => $providerUser->getName(),
            'provider_nickname' => $providerUser->getNickname(),
            'provider_email' => $providerUser->getEmail(),
            'provider_avatar' => $providerUser->getAvatar(),
            'provider_user' => $providerUser,
        ]);

        if ($thirdPartyAuthConnection->user_id) {
            // all good, just login
        } else {
            // create the user
            $user = new User([
                'name' => $providerUser->name,
                'email' => $providerUser->email,
            ]);

            $user->save();
            $thirdPartyAuthConnection->user_id = $user->id;
            $thirdPartyAuthConnection->save();
        }

        dd($thirdPartyAuthConnection->toArray());
    }
}

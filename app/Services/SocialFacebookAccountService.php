<?php

namespace App\Services;

use App\User;
use Auth;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialFacebookAccountService
{
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = User::where('login_type','facebook')
            ->where('facebook_id',$providerUser->getId())
            ->first();


        if ($account) {
            return $account->user;
        } else {

            $account = new User([
                'facebook_id' => $providerUser->getId(),
                'login_type' => 'facebook'
            ]);



            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user) {

                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name' => $providerUser->getName(),
                    'facebook_id' => $providerUser->getId(),
                ]);
            }

            return $user;
        }
    }
}
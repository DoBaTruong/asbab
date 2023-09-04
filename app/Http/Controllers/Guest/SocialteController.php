<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SocialAuth;
use GuzzleHttp\Client;
use Socialite;
use Hash;
use DB;
use Log;

class SocialteController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $getInfor = Socialite::driver($provider)->user();
        $user = $this->createOrGetUser($getInfor, $provider);
        auth()->login($user);

        if(auth()->user()->avatar == null) {
            $path = 'images/avatar/'.time().'.jpg';
            $client = new Client([
                'verify' => false
            ]);
    
            $res = $client->request('GET', $getInfor->getAvatar(), [
                'sink' => public_path($path)
            ]);
            $user->update([
                'avatar' => $path
            ]);
        }
        
        return redirect()->route('asbab.home');
    }

    public function createOrGetUser($getInfor, $provider)
    {
        try {
            DB::beginTransaction();
            $getUser = SocialAuth::whereProvider($provider)->whereProviderUserId($getInfor->getId())->first();
            if (!$getUser) {
                $user = User::where('email', $getInfor->getEmail())->first();
                if(!isset($user)) {
                    $user = User::create([
                        'name' => $getInfor->getName(),
                        'email' => $getInfor->getEmail(),
                        'email_verified_at' => now(),
                        'type' => 5,
                        'password' => Hash::make('123456')
                    ]);
                }
                
                SocialAuth::create([
                    'provider' => $provider,
                    'provider_user_id' => $getInfor->getId(),
                    'user_id' => $user->id
                ]);
            } else {
                $user = $getUser->users;
            }
            DB::commit();
            return $user;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
            ], 500);
        }
    }
}

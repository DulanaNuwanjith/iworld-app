<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SmsHelper
{
    public static function sendSms($phone, $message)
    {
        $response = Http::post(env('NOTIFYLK_API_URL'), [
            'user_id'   => env('NOTIFYLK_USER_ID'),
            'api_key'   => env('NOTIFYLK_API_KEY'),
            'sender_id' => env('NOTIFYLK_SENDER_ID'),
            'to'        => $phone,
            'message'   => $message,
        ]);

        return $response->json();
    }
}

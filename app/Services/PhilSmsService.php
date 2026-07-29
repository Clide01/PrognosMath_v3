<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSmsService
{
    /**
     * Send an SMS to any number using PhilSMS
     */
    public function sendSms($phoneNumber, $message)
    {
        // 1. Safely retrieve config with strict fallbacks
        $url = config('services.philsms.url', env('PHILSMS_URL', 'https://app.philsms.com/api/v3/sms/send'));
        $token = config('services.philsms.token', env('PHILSMS_TOKEN'));

        // 2. Prevent HTTP crash if Token is completely missing
        if (empty($token)) {
            Log::error('PhilSMS Configuration Error: API Token is missing.');
            return false;
        }

        try {
            // 3. Execute HTTP request cleanly
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post($url, [
                'recipient' => $phoneNumber,
                'sender_id' => 'PhilSMS', // Replace if you have a custom approved sender ID
                'type'      => 'plain',
                'message'   => $message
            ]);

            if (!$response->successful()) {
                Log::error('PhilSMS API Failed: ' . $response->body());
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('PhilSMS Gateway Error: ' . $e->getMessage());
            return false;
        }
    }
}
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;

class SendWhatsAppNotification implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $message;

    public function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function handle()
    {
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $accessToken = env('WHATSAPP_ACCESS_TOKEN');

        $url = "https://graph.facebook.com/v16.0/{$phoneNumberId}/messages";

        $client = new Client();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->user->phone,
                    'type' => 'text',
                    'text' => [
                        'body' => $this->message,
                    ],
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            // Log detailed response
            \Log::info("WhatsApp message sent successfully", [
                'user' => $this->user->id,
                'phone' => $this->user->phone,
                'message' => $this->message,
                'response' => $responseBody,
            ]);
        } catch (\Exception $e) {
            // Log detailed error
            \Log::error("Failed to send WhatsApp message", [
                'user' => $this->user->id,
                'phone' => $this->user->phone,
                'message' => $this->message,
                'error' => $e->getMessage(),
            ]);
        }
    }

}

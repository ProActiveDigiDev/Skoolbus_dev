<?php

namespace App\Http\Controllers;

use Twilio\Rest\Client;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function sendWhatsAppMessage($to_num, $message)
    {
        $twilioSid = env('TWILIO_SID');
        $twilioToken = env('TWILIO_AUTH_TOKEN');
        $twilioWhatsAppNumber = "whatsapp:". env('TWILIO_WHATSAPP_NUMBER');
        $recipientNumber = "whatsapp:". ($to_num ?? env('TWILIO_TEST_RECIPIENT')); 

        $twilio = new Client($twilioSid, $twilioToken);

        try {
           $message = $twilio->messages->create(
                $recipientNumber,
                array(
                    "from" => $twilioWhatsAppNumber,
                    "body" => $message,
                )
            );
            
            return $message;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

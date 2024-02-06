<?php

use App\Http\Controllers\WhatsAppController;


if(!function_exists('encID')){
    function encID($userId, $type) {
        if (!isset($type) || ($type != 'enc' && $type != 'decr')) {
            return false;
        }
    
        $key = env("RIDER_ENCRYPTION_KEY");
        $keyLength = strlen($key);
    
        if ($type == 'enc') {
            $frnt = 'sklbs_' . rand(10, 99) . chr(rand(65, 90));
            $bck = rand(10, 99) . chr(rand(65, 90));
            $userId = strval($userId);
            $userId = $frnt . '_' . $userId . '_' . $bck;
        } else if ($type == 'decr') {
            $userId = base64_decode(urldecode($userId));
        }
    
        $result = '';
    
        for ($i = 0; $i < strlen($userId); $i++) {
            $result .= $userId[$i] ^ $key[$i % $keyLength];
        }
    
        if ($type == 'enc') {
            $result = urlencode(base64_encode($result));
        } else if ($type == 'decr') {
            // Extract prefix and postfix lengths dynamically
            $prefixLength = strpos($result, '_') + 5;
            $postfixLength = strrpos($result, '_') - strlen($result);
            
            // Adjust the substrings
            $result = substr($result, $prefixLength);
            $result = substr($result, 0, $postfixLength);
        }
        return $result;
    }
    
    
}

if(!function_exists('sendWhatsAppNotification')){
    function sendWhatsAppNotification($to_num, $message)
    {
        //if in dev mode, send to test number
        if(env('APP_ENV') == 'local'){
            $to_num = env('TEST_WHATSAPP_NUMBER');
        }

        //send notification to rider parent
        $whatsapp = new WhatsAppController();
        return $whatsapp->sendWhatsAppMessage($to_num, $message);

    }
}
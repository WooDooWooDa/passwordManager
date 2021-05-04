<?php namespace Models;

use Twilio\Rest\Client;
use Zephyrus\Security\Cryptography;

class SmsAuthentication
{
    const TWILIO_NUMBER = "+14159426361";
    const TWILIO_SID = "AC0689726e68241866d94c871a90a40d71";
    const TWILIO_TOKEN = "cd5a2d79c6acb3bf1d4b9b1ce614ba96";

    public function createSms($phone): string
    {
        $code = Cryptography::randomString(6, "1234567890");
        $sid = getenv('TWILIO_ACCOUNT_SID');
        $token = getenv('TWILIO_AUTH_TOKEN');
        $client = new Client(self::TWILIO_SID,self::TWILIO_TOKEN);
        $client->messages->create($phone, [
            'from' => self::TWILIO_NUMBER,
            'body' => "Voici votre code : " . $code
        ]);
        return $code;
    }
}
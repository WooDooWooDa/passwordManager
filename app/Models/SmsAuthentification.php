<?php namespace Models;

use Twilio\Rest\Client;
use Zephyrus\Security\Cryptography;

class SmsAuthentification
{
    const TWILIO_NUMBER = "+14159426361";
    const TWILIO_SID = "AC0689726e68241866d94c871a90a40d71";
    const TWILIO_TOKEN = "a8d8a52708466c6bad45fc83914b2bdb";

    public function createSms($phone): string
    {
        $code = Cryptography::randomString(6, "1234567890");
        $client = new Client(self::TWILIO_SID, self::TWILIO_TOKEN);
        $client->messages->create($phone, [
            'from' => self::TWILIO_NUMBER,
            'body' => "Voici votre code : " . $code
        ]);
        return $code;
    }
}
<?php namespace Models;

use Twilio\Rest\Client;
use Zephyrus\Security\Cryptography;

class SmsAuthentication
{
    const TWILIO_NUMBER = "+14159426361";

    public function createSms($phone): string
    {
        $code = Cryptography::randomString(6, "1234567890");
        $client = new Client(getenv('TWILIO_ACCOUNT_SID'),getenv('TWILIO_AUTH_TOKEN'));
        $client->messages->create($phone, [
            'from' => self::TWILIO_NUMBER,
            'body' => "Voici votre code : " . $code
        ]);
        return $code;
    }
}
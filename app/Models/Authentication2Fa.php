<?php namespace Models;

use SendGrid;
use Twilio\Rest\Client;
use Zephyrus\Security\Cryptography;

class Authentication2Fa
{
    const TWILIO_NUMBER = "+14159426361";
    const EMAIL_FROM = "jeremie-bouchard@hotmail.fr";

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

    public function createEmail($emailUser): string
    {
        $code = Cryptography::randomString(6, "1234567890");
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(self::EMAIL_FROM, "PasswordManager");
        $email->setSubject("Code de vérification à deux facteurs - Password Manager");
        $email->addTo($emailUser);
        $email->addContent("text/plain", "Voici votre code de vérification : $code");
        //send
        $sendgrid = new SendGrid(getenv('EMAIL_API_KEY'));
        $response = $sendgrid->send($email);
        return $code;
    }
}
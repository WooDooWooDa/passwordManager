<?php namespace Models\Brokers;


use Zephyrus\Security\Cryptography;

class TokenBroker extends Broker
{
    public function getAllTokenByUserId($id): array
    {
        $sql = "SELECT date, ip, cookie_token, user_agent from passwordmanagerdb.token where user_id = '$id'";
        return $this->select($sql);
    }

    public function deleteToken($token)
    {
        $sql = "DELETE FROM passwordmanagerdb.token WHERE cookie_token = '$token'";
        $this->query($sql);
    }

    public function remember($userId): string
    {
        $cookie = Cryptography::randomString(64);
        $sql = "INSERT INTO passwordmanagerdb.token(user_id, cookie_token, date, ip, user_agent) VALUES(?, ?, ?, ?, ?)";
        $this->query($sql, [$userId, $cookie, date('m/d/Y h:i:s'), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
        return $cookie;
    }

    public function unremembered($cookie)
    {
        $sql = "DELETE FROM passwordmanagerdb.token where cookie_token = '$cookie'";
        $this->query($sql);
    }
}
<?php namespace Models\Brokers;


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
}
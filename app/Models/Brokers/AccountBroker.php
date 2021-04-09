<?php namespace Models\Brokers;

class AccountBroker extends Broker
{
    public function findById($id): ?\stdClass
    {
        $sql = "SELECT * from authentication where user_id = ?";
        return $this->selectSingle($sql, [$id]);
    }

    public function registerNew($username, $password)
    {
        $saltedPassword = password_hash($password . PASSWORD_PEPPER, PASSWORD_DEFAULT);
        $userSql = "INSERT INTO authentication(user_id, username, password, firstname, lastname) VALUES(default, ?, ?, ?, ?)";
        $this->query($userSql ,[$username, $saltedPassword, $_SESSION["firstname"], $_SESSION["lastname"]]);
    }

    public function findByUsername($username): ?\stdClass
    {
        $sql = "SELECT * from authentication where username = ?";
        return $this->selectSingle($sql, [$username]);
    }
}

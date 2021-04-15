<?php namespace Models\Brokers;

use stdClass;

class AccountBroker extends Broker
{
    public function findById($id): ?\stdClass
    {
        $sql = "SELECT * from passwordmanagerdb.authentication where user_id = ?";
        return $this->selectSingle($sql, [$id]);
    }

    public function registerNew(stdClass $user)
    {
        $saltedPassword = password_hash($user->password . PASSWORD_PEPPER, PASSWORD_DEFAULT);
        $userSql = "INSERT INTO passwordmanagerdb.authentication(user_id, username, password, firstname, lastname) VALUES(default, ?, ?, ?, ?)";
        $this->query($userSql ,[$user->username, $saltedPassword, $user->firstname, $user->lastname]);
    }

    public function updateAccount(stdClass $user)
    {
        $saltedPassword = password_hash($user->password . PASSWORD_PEPPER, PASSWORD_DEFAULT);
        $userId = sess('user_id');
        $userSql = "UPDATE passwordmanagerdb.authentication SET username = ?, password = ?, firstname = ?, lastname = ?, email = ? where user_id = '$userId'";
        $this->query($userSql ,[$user->username, $saltedPassword, $user->firstname, $user->lastname, $user->email]);
    }

    public function findByUsername($username): ?\stdClass
    {
        $sql = "SELECT * from passwordmanagerdb.authentication where username = ?";
        return $this->selectSingle($sql, [$username]);
    }
}

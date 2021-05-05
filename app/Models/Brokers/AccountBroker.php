<?php namespace Models\Brokers;

use stdClass;
use Zephyrus\Security\Cryptography;

class AccountBroker extends Broker
{
    public function findById($id): ?\stdClass
    {
        $sql = "SELECT * from passwordmanagerdb.authentication where user_id = ?";
        return $this->selectSingle($sql, [$id]);
    }

    public function registerNew(stdClass $user)
    {
        $saltedPassword = password_hash($user->password . getenv('PASSWORD_PEPPER'), PASSWORD_DEFAULT);
        $userSql = "INSERT INTO passwordmanagerdb.authentication(user_id, username, password, firstname, lastname, email, phone, authType) VALUES(default, ?, ?, ?, ?, ?, ?, 0)";
        $this->query($userSql ,[$user->username, $saltedPassword, $user->firstname, $user->lastname, $user->email, str_replace(['-', ' ', '(', ')'], '', $user->phone)]);
        $salt = Cryptography::randomString(64);
        $sql = "INSERT INTO passwordmanagerdb.salt(user_id, salt) VALUES((SELECT last_value from passwordmanagerdb.authentication_user_id_seq), ?)";
        $this->query($sql, [$salt]);
    }

    public function updateAccount(stdClass $user)
    {
        $saltedPassword = password_hash($user->password . getenv('PASSWORD_PEPPER'), PASSWORD_DEFAULT);
        $userId = sess('user_id');
        if (str_contains($user->password, "*")) {
            $userSql = "UPDATE passwordmanagerdb.authentication SET username = ?, firstname = ?, lastname = ?, email = ? where user_id = '$userId'";
            $this->query($userSql ,[$user->username, $user->firstname, $user->lastname, $user->email]);
        } else {
            $userSql = "UPDATE passwordmanagerdb.authentication SET username = ?, password = ?, firstname = ?, lastname = ?, email = ? where user_id = '$userId'";
            $this->query($userSql ,[$user->username, $saltedPassword, $user->firstname, $user->lastname, $user->email]);
        }
    }

    public function findByUsername($username): ?\stdClass
    {
        $sql = "SELECT * from passwordmanagerdb.authentication where username = ?";
        return $this->selectSingle($sql, [$username]);
    }

    public function findByToken($cookie): ?\stdClass
    {
        $sql = "SELECT * from passwordmanagerdb.authentication a join passwordmanagerdb.token t on a.user_id = t.user_id where cookie_token = ?";
        return $this->selectSingle($sql, [$cookie]);
    }

    public function getKey($password, $id): string
    {
        $sql = "SELECT salt FROM passwordmanagerdb.salt WHERE user_id = ?";
        $result = $this->selectSingle($sql, [$id]);
        return Cryptography::deriveEncryptionKey($password, $result->salt);
    }

    public function updateAuth($authType, $id)
    {
        $sql = "UPDATE passwordmanagerdb.authentication set authType = ? WHERE user_id = ?";
        $this->query($sql, [$authType, $id]);
    }
}

<?php namespace Models\Brokers;

use Zephyrus\Security\Cryptography;

class ServiceBroker extends Broker
{
    public function getAllService(): array
    {
        $sql = "SELECT * from passwordmanagerdb.service";
        return $this->select($sql);
    }

    public function getServiceById($id): ?\stdClass
    {
        $sql = "SELECT s.id, s.name, s.img, s.url from passwordmanagerdb.service s left join passwordmanagerdb.service_information si on si.id_service = s.id where s.id = '$id'";
        return $this->selectSingle($sql);
    }

    public function getServiceInfoFor($serviceId, $userId): ?\stdClass
    {
        $sql = "SELECT * from passwordmanagerdb.service_information si where si.id_service = '$serviceId' and user_id = '$userId'";
        return $this->selectSingle($sql);
    }

    public function getServiceByIdAndUser($serviceId, $userId): ?\stdClass
    {
        $sql = "SELECT * from passwordmanagerdb.service_information si where si.id_service = '$serviceId' and si.user_id = '$userId'";
        return $this->selectSingle($sql);
    }

    public function getAllServiceWithInfo($userId): array
    {
        $sql = "SELECT * from passwordmanagerdb.service s left join passwordmanagerdb.service_information si on si.id_service = s.id and si.user_id = '$userId'";
        return $this->select($sql);
    }

    public function insert($serviceId, $userId, $form)
    {
        $key = Cryptography::deriveEncryptionKey($form->password, "saltTemporary");
        $passwordFirstEncrypt = Cryptography::encrypt($form->password, $key);
        $passwordEncrypted = Cryptography::encrypt($passwordFirstEncrypt, $key);
        $sql = "INSERT INTO passwordmanagerdb.service_information(id_service_information, id_service, username, password, user_id, key) values(default, ?, ?, ?, ?, ?)";
        $this->query($sql, [$serviceId, $form->username, $passwordEncrypted, $userId, $key]);
    }

    public function update($userId, $id, $service)
    {
        $key = Cryptography::deriveEncryptionKey($service->password, "saltTemporary");
        $passwordFirstEncrypt = Cryptography::encrypt($service->password, $key);
        $passwordEncrypted = Cryptography::encrypt($passwordFirstEncrypt, $key);
        $sql = "UPDATE passwordmanagerdb.service_information set username = ?, password = ?, set key = ? WHERE user_id = '$userId' and id_service = '$id'";
        $this->query($sql, [$service->username, $passwordEncrypted, $key]);
    }

    public function getPasswordDecrypted($service): String {
        $sql = "SELECT key from passwordmanagerdb.service_information where id_service_information = '$service->id_service_information'";
        $result = $this->selectSingle($sql);
        $passwordDecryptedOnce  = Cryptography::decrypt($service->password, $result->key);
        return Cryptography::decrypt($passwordDecryptedOnce, $result->key);
    }
}
<?php namespace Models\Brokers;

class ServiceBroker extends Broker
{
    public function getAllService(): array
    {
        $sql = "SELECT * from passwordmanagerdb.service";
        return $this->select($sql);
    }

    public function getServiceById($id): \stdClass
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
        $sql = "INSERT INTO passwordmanagerdb.service_information(id_service, username, password, user_id) values(?, ?, ?, ?)";
        $this->query($sql, [$serviceId, $form->username, $form->password, $userId]);
    }

    public function update($userId, $id, $service)
    {
        $sql = "UPDATE passwordmanagerdb.service_information set username = ?, password = ? WHERE user_id = '$userId' and id_service = '$id'";
        $this->query($sql, [$service->username, $service->password]);
    }
}
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
        $sql = "SELECT * from passwordmanagerdb.service s left join passwordmanagerdb.service_information si on si.id_service = s.id where s.id = '$id'";
        return $this->selectSingle($sql);
    }

    public function getAllServiceWithInfo($userId): array
    {
        $sql = "SELECT * from passwordmanagerdb.service s left join passwordmanagerdb.service_information si on si.id_service = s.id and si.user_id = '$userId'";
        return $this->select($sql);
    }
}
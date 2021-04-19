<?php namespace Controllers;

use Models\Brokers\ServiceBroker;

class ServiceController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->get("/home/service/{id}", "singleService");
        $this->post("/service/show", "showMdp");
    }

    public function singleService($id) {
        $broker = new ServiceBroker();
        $service = $broker->getServiceById($id);
        return $this->render('singleService', [
            'service' => $service
        ]);
    }

    public function showMdp() {
        $form = $this->buildForm();
        $array = $_SESSION['showMdp'];
        $index = $form->getValue('show');
        $array[$index] = !$array[$index];
        $_SESSION['showMdp'] = $array;
        return $this->redirect("/home/service");
    }
}
<?php namespace Controllers;

use Models\Brokers\ServiceBroker;
use Zephyrus\Application\Flash;

class ServiceController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->get("/home/service", "service");
        $this->get("/home/service/{id}", "singleService");
        $this->post("/service/show", "showMdp");
        $this->put("/service/update/{id}", "update");
    }

    public function service()
    {
        if (!isset($_SESSION["is_logged"])) {
            return $this->redirect("/login");
        }
        if (!isset($_SESSION['showMdp'])) {
            $_SESSION['showMdp'] = [false, false, false, false];
        }
        $broker = new ServiceBroker();
        $services = $broker->getAllServiceWithInfo(sess('user_id'));
        return $this->render('service', [
            'title' => "Services - Password Manager",
            'services' => $services,
            'show' => sess('showMdp')
        ]);
    }

    public function singleService($id) {
        $broker = new ServiceBroker();
        $service = $broker->getServiceById($id);
        $serviceInfo = $broker->getServiceInfoFor($id, sess('user_id'));
        return $this->render('singleService', [
            'service' => $service,
            'serviceInfo' => $serviceInfo
        ]);
    }

    public function showMdp() {
        $form = $this->buildForm();
        $array = $_SESSION['showMdp'];
        $index = $form->getValue('show');
        $array[$index] = !$array[$index];
        $_SESSION['showMdp'] = $array;
        return $this->redirect("/home/service#" . $index);
    }

    public function update($id)
    {
        $form = $this->buildForm();
        if (!$form->getValue('comfirm')) {
            Flash::error('Veuillez comfirmer les changements avant de les appliquer');
            return $this->redirect("/home/service/" . $id);
        }
        $broker = new ServiceBroker();
        if (!is_null($broker->getServiceByIdAndUser($id, sess('user_id')))) {
            $broker->update(sess('user_id'), $id, $form->buildObject());
        } else {
            $broker->insert($id, sess('user_id'), $form->buildObject());
        }
        Flash::success("Service mis à jour avec succès");
        return $this->redirect("/home/service/" . $id);
    }
}
<?php
class ClientController {
    private $model;

    public function __construct() {
        $this->model = new Client();
    }

    public function create() {
        $clients = $this->model->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create($_POST['name'], $_POST['car_model'], $_POST['phone'], $_POST['address']);
            header('Location: index.php?route=clients_create');
            exit;
        }
        $client = null;
        include 'views/client_form.php';
    }

    public function edit($id) {
        $client = $this->model->getById($id);
        $clients = $this->model->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, $_POST['name'], $_POST['car_model'], $_POST['phone'], $_POST['address']);
            header('Location: index.php?route=clients_create');
            exit;
        }
        include 'views/client_form.php';
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?route=clients_create');
        exit;
    }
}
<?php
class RepairTypeController {
    private $model;

    public function __construct() {
        $this->model = new RepairType();
    }

    public function create() {
        $types = $this->model->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create($_POST['name'], $_POST['default_price']);
            header('Location: index.php?route=types_create');
            exit;
        }
        $type = null;
        include 'views/repair_type_form.php';
    }

    public function edit($id) {
        $type = $this->model->getById($id);
        $types = $this->model->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, $_POST['name'], $_POST['default_price']);
            header('Location: index.php?route=types_create');
            exit;
        }
        include 'views/repair_type_form.php';
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?route=types_create');
        exit;
    }
}
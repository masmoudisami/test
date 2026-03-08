<?php
require_once 'config.php';
require_once 'models/Database.php';
require_once 'models/Invoice.php';
require_once 'models/Client.php';
require_once 'models/RepairType.php';
require_once 'controllers/InvoiceController.php';
require_once 'controllers/ClientController.php';
require_once 'controllers/RepairTypeController.php';

$route = $_GET['route'] ?? 'invoices';
$id = $_GET['id'] ?? null;

switch ($route) {
    case 'invoices':
        (new InvoiceController())->index();
        break;
    case 'invoices_create':
        (new InvoiceController())->create();
        break;
    case 'invoices_edit':
        (new InvoiceController())->edit($id);
        break;
    case 'invoices_delete':
        (new InvoiceController())->delete($id);
        break;
    case 'invoices_print':
        (new InvoiceController())->print($id);
        break;
    case 'search_clients':
        (new InvoiceController())->searchClients();
        break;
    case 'client_history':
        (new InvoiceController())->clientHistory();
        break;
    case 'clients_create':
        (new ClientController())->create();
        break;
    case 'clients_edit':
        (new ClientController())->edit($id);
        break;
    case 'clients_delete':
        (new ClientController())->delete($id);
        break;
    case 'types_create':
        (new RepairTypeController())->create();
        break;
    case 'types_edit':
        (new RepairTypeController())->edit($id);
        break;
    case 'types_delete':
        (new RepairTypeController())->delete($id);
        break;
    default:
        (new InvoiceController())->index();
        break;
}
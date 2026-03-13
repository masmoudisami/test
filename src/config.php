<?php
define('DB_HOST', 'db'); // 'db' à modifier par 'localhost' en version web
define('DB_NAME', 'mechanic_db');
define('DB_USER', 'sami');
define('DB_PASS', 'Sm/131301');
define('APP_URL', 'http://localhost:8080');
define('CURRENCY', 'TND');
date_default_timezone_set('Africa/Tunis');
header('Content-Type: text/html; charset=utf-8');

// Informations du Garagiste (Personnalisation Facture)
define('GARAGE_NAME', 'GARAGE AUTO SERVICE');
define('GARAGE_ADDRESS', 'Avenue Habib Bourguiba, Tunis 3000');
define('GARAGE_PHONE', '+216 71 123 456');
define('GARAGE_EMAIL', 'contact@garage-auto.tn');
define('GARAGE_LOGO', 'assets/logo.png'); // Chemin relatif du logo
define('GARAGE_MATRICULE', 'M1234567'); // Matricule fiscal optionnel

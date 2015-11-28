<?php
/* Conectar a una base de datos de ODBC invocando al controlador */
$server = 'internal-db.s209267.gridserver.com';
$database = 'db209267_proactivar';
$user = 'db209267_proact';
$password = 'ProAct_123';
$dns = 'mysql:dbname='.$database.';host='.$server;
try {
	$BD = new PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
} catch (PDOException $e) {echo 'connection failed: ' . $e->getMessage();}
<?php

$config = include '../env.php';

$HOST =  $config['DB_HOST'];
$NAME =  $config['DB_NAME'];
$USER =  $config['DB_USER'];
$PASSWORD =  $config['DB_PASSWORD'];

$cn = new mysqli($HOST, $USER, $PASSWORD, $NAME);

if ($cn->connect_errno) {
	echo "Fallo al conectar a MySQL: (" . $cn->connect_errno . ") " . $cn->connect_error;
}

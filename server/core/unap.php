<?php

$config = include '../env.php';

$HOST =  $config['DB_HOST'];
$NAME =  $config['DB_NAME_UNAP'];
$USER =  $config['DB_USER'];
$PASSWORD =  $config['DB_PASSWORD'];

$unap = new mysqli($HOST, $USER, $PASSWORD, $NAME);

if ($unap->connect_errno) {
	echo "Fallo al conectar a MySQL: (" . $unap->connect_errno . ") " . $unap->connect_error;
}
else{
	echo "Conectado a unap";

}

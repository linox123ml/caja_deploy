<?php
	// $cn = new mysqli('localhost', 'root', '', 'netcaja');
	$cn = new mysqli('64.20.56.124', 'lino', 'caja2023lnx', 'netcaja');
	// $cn = new mysqli('localhost', 'root', 'Universidades', 'netcaja');
	// $cn = new mysqli('137.184.129.66', 'elvis', 'TiYsLawxrF', 'caja');
	
	if ($cn->connect_errno) {
	    echo "Fallo al conectar a MySQL: (" . $cn->connect_errno . ") " . $cn->connect_error;
	}
?>

<?php
/*
 * Consulta de SERVICIO de pago por ADMISION
 * EJEMPLO:
 * 			http://<IP del SISTEMA de CAJA>/CHECK_PAYMENT/?w=
 * DONDE:
 * 			/CHECK_PAYMENT/ es esta carpeta, que puede ser renombrada
 * 			/?w=<DNI CONSULTADO>, el parametro de consulta es w
 * 			se valida el DNI a 6 como posible Código de Matricula  
 */
define('SERVER_DB', '127.0.0.1');
define('PORT_DB', 3306);
define('DATABASE_NAME', 'netcaja');
define('USER_DB', 'root');
define('PASSWORD_DB', '');
define('DSN_DB', 'mysql:host=' . SERVER_DB . ':' . @strval(PORT_DB) . ';dbname=' . DATABASE_NAME . ';charset=utf8');

# Recuperar la llamada del REQUEST
$solicitud = strtoupper( @strval( $_SERVER['REQUEST_METHOD'] ));
switch ( $solicitud )
{
	case 'GET' :
	{		
		# die( $_SERVER['QUERY_STRING'] );
		$id = $_GET['w']; // ctype_digit($id)
		if( !is_numeric($id) && !is_int($id) || strlen($id) < 6) {
			
			http_response_code(403);
			die( json_encode( [ 'error' => 'Solicitud [de parámetro incoherente]!' ] ) );

		}

		/*
		 * Continua con la consulta	
		 */
		try {
			$conn = new PDO ( DSN_DB, USER_DB, PASSWORD_DB );
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(Exception $errorConnection) {
			http_response_code(500);
			die(json_encode( array('msg' => $errorConnection->getMessage() )));
			# return ;
		}	
		$strQuery = "
			SELECT DISTINCT 
				`idpapeleta` AS paymentID, 
				CONCAT(`serie`, '-', `numero`) AS paymentTitle,
				`hora` AS paymentDatetime,
				CAST(`total` AS DECIMAL(10, 2)) AS paymentAmount  
			FROM `teso_papeletapago`
			WHERE `tipo`=4 AND `fecha` >= '2023-07-10' AND `codigo` LIKE CONCAT('%', ?, '%') OR `idcodigo` LIKE CONCAT('%', ?, '%')
		";
		try {
			$stmt = $conn->prepare($strQuery);
			$stmt->bindParam(1, $id, PDO::PARAM_INT);
			$stmt->bindParam(2, $id, PDO::PARAM_INT);
			$stmt->execute();
			# $rsTmp = $conn->query("SELECT count(*) AS quantity FROM `Academical_Structure` WHERE `AcademicalOrder`='003' AND `AcademicalLevel`='001'")->fetch(PDO::FETCH_ASSOC);
			# $total_items = @intval($rsTmp['quantity']);
		} catch(Exception $error) {
			http_response_code(500);
			die(json_encode( array('msg' => $error->getMessage() )));
			# return ;
		}

		/**
		 * Genera el __Conjunto de datos resultantes__ (SQL Resultset)
		 */
		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$sizeResultSet = @intval(count($rs));

		$conn = null;
		unset($conn);

		http_response_code(200);
		header('Content-Type: application/json; charset="UTF-8"');
		
		echo json_encode( [ 'data' => $sizeResultSet === 0 ? [] : $rs, ]);
		// 'delay' => ($endTime - $startTime), 'total' => $total_items, 
		# break;
    }

    case 'POST' : {
		break;	
    }

	/*
    default: {
		http_response_code(401);
		die( json_encode( [ 'error' => 'Acceso denegado | Solicitud prohibida!' ] ) );
	}
	*/
}

?>

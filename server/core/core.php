<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CORE
{
    protected $response;
    protected $mode;

    public function __construct()
    {
        $config = include '../env.php';

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET,POST");
        header("Access-Control-Allow-Headers: Content-Type");
        $this->mode =  $config['MODE'];
        $this->response = [];
    }

    public function getCurrentUser()
    {
        session_start();

        if (!isset($_SESSION['idusuario'])) {
            $this->response['success'] = false;
            $this->response['message'] = 'No autorizad(a)';
            $this->response['data'] = null;
            echo json_encode($this->response);
            exit;
        } else {
            $this->response['success'] = true;
            $this->response['message'] = 'Biemvenid(a) ...';
            $this->response['data'] = [
                'user' => $_SESSION['idusuario'],
            ];
            echo json_encode($this->response);
            exit;
        }
    }

    public function getOtraPersona($term)
    {
        include 'cn.php';
        $sqlSelect = "select idotro, codigo, nombre from teso_otrapersona ";
        $sqlSelect .= "where codigo = '$term';";
        $result = $cn->query($sqlSelect);
        $row = $result->fetch_array();
        if ($row === null) {
            $this->response['success'] = false;
            $this->response['message'] = 'Otra persona NO encontrad(a)';
            $this->response['data'] = null;
            echo json_encode($this->response);
        } else {
            $this->response['success'] = true;
            $this->response['message'] = 'Otra persona encontrad(a)';
            $this->response['data'] = $row;
            echo json_encode($this->response);
        }
    }

    //Pagos inscripcion
    public function savePago($person, $details)
    {
        if ($this->mode === 'production') {
            session_start();
            if (!isset($_SESSION['idusuario'])) {
                $this->response['success'] = false;
                $this->response['message'] = 'No autorizad(a)';
                $this->response['data'] = null;
                echo json_encode($this->response);
                exit;
            }
        }

        include 'cn.php';

        $cn->begin_transaction();
        try {

            $pagoPapeleta = $this->savePapeletaPago($person, $cn);

            foreach ($details as $value) {
                $this->saveDetaPapeletaPago($pagoPapeleta->idpadre, $value, $cn);
            }

            $this->response['success'] = true;
            $this->response['message'] = 'Pago regitrado con exito';
            $this->response['data'] = $pagoPapeleta;

            $cn->commit();
            $cn->close();

            echo json_encode($this->response);
        } catch (\Throwable $th) {
            $this->response['success'] = false;
            $this->response['message'] = $th;
            $this->response['data'] = null;
            $cn->rollback();
            $cn->close();
            echo json_encode($this->response);
        }
    }

    public function savePapeletaPago($person, $cn)
    {

        if ($this->mode === 'production') {
            session_start();
            $var_anio = $_SESSION['anio'];
            $idusuario = $_SESSION['idusuario'];
        } else {
            $var_anio =  '2023';
            $idusuario =  '0028';
        }

        $fecha     = date('Y-m-d');

        $newPerson =  $this->saveOtraPersona($person);

        $idtipo    = 4; //default: otra persona
        $idcodigo = $newPerson['idotro'];
        $nombrelimpio = str_replace("'", "", $person->nombres);
        $nombrelimpio = str_replace('"', "", $nombrelimpio);
        $nombrelimpio = str_replace('`', "", $nombrelimpio);
        $nombrelimpio = str_replace('´', "", $nombrelimpio);
        $nombre    = strtoupper($nombrelimpio);
        $codigo    = $person->nro_doc;
        //$nombre_limpio =  preg_replace("/[^a-zA-Z\s]/", "", $person->nombres);
        //$nombre    = $nombre_limpio;
        $clave = $this->generar_clave();
        $obs    = ""; //default: sin observaciones

        $ip = $_SERVER['REMOTE_ADDR'];

        $sql  = "select teso_caja.serie from teso_usuariocaja left join teso_caja on teso_caja.idcaja=teso_usuariocaja.idcaja where teso_usuariocaja.idusuario ='$idusuario'";
        $result = $cn->query($sql);
        $row = $result->fetch_array();
        $serie = $row['serie'];

        # Generamos el numero
        $sql = "select max(numero) as maxnum from teso_papeletapago where anio='$var_anio' and serie='$serie'";
        $result = $cn->query($sql);
        $row = $result->fetch_array();
        $numero = str_pad($row['maxnum'] + 1, 6, '0', STR_PAD_LEFT);

        $sql = "insert into teso_papeletapago (anio,serie,numero,fecha,obs,hora,estado,clave,tipo,idcodigo,codigo,idusuario, nombre,ip) ";
        $sql .= "values ('$var_anio','$serie','$numero','$fecha','$obs',CURRENT_TIMESTAMP,'0','$clave','$idtipo','$idcodigo','$codigo','$idusuario', '$nombre','$ip') ";

        $cn->query($sql);

        $sql = "select max(idpapeleta) as maxid from teso_papeletapago";
        $result = $cn->query($sql);
        $row = $result->fetch_array();
        $newid = $row['maxid'];

        $datos = (object) [
            'idpadre' => $newid,
            'serie' => $serie,
            'numero' => $numero,
            'clave' => $clave,
            'error' => $cn->error,
            'newPerson' => $newPerson['idotro'],
        ];

        return $datos;
    }

    public function saveDetaPapeletaPago($papeleta, $detail, $cn)
    {

        $idpadre = $papeleta;
        $idtarifa = $detail->value;
        $cantidad = 1; //Default: 1
        $precio = $detail->price;
        // $detalle = $detail->title;
        $detalle = $detail->detail;

        $sql = "insert into teso_papeletatarifas (idpapeleta,idtarifa,cantidad,precio,detalle) ";
        $sql .= "values ('$idpadre','$idtarifa','$cantidad','$precio','$detalle') ";
        $cn->query($sql);

        $sql = "select IF(SUM(round(cantidad*precio,2)) IS NULL,0.00,SUM(round(cantidad*precio,2))) total from teso_papeletatarifas where idpapeleta='$idpadre'";
        $result = $cn->query($sql);
        $row = $result->fetch_array();
        $importe = $row['total'];

        $sql = "update teso_papeletapago set ";
        $sql .= "total='$importe' ";
        $sql .= "where idpapeleta='$idpadre'";
        $cn->query($sql);
    }

    public function saveOtraPersona($person)
    {
        include 'cn.php';

        $newPerson = null;

        $sqlSelect = "select idotro, codigo from teso_otrapersona ";
        $sqlSelect .= "where codigo = '$person->nro_doc';";
        $result = $cn->query($sqlSelect);
        $row = $result->fetch_array();

        if ($row === null) {
            $codigo = $person->nro_doc;
            $nombrelimpio = str_replace("'", "", $person->nombres);
            $nombrelimpio = str_replace('"', "", $nombrelimpio);
            $nombrelimpio = str_replace('`', "", $nombrelimpio);
            $nombrelimpio = str_replace('´', "", $nombrelimpio);
            $nombre    = strtoupper($nombrelimpio);
            //$nombrelimpio = preg_replace("/[^a-zA-Z\s]/", "", $person->nombres);
            //$nombre =  strtoupper($nombrelimpio);
            $direccion  = null;
            $email  = null;
            $telefono   = null;

            $sql = "insert into teso_otrapersona (codigo,nombre,direccion,email,telefono) ";
            $sql .= "values ('$codigo','$nombre','$direccion','$email','$telefono') ";
            $cn->query($sql);

            $sqlSelect = "select idotro, codigo from teso_otrapersona ";
            $sqlSelect .= "where codigo = '$person->nro_doc';";
            $result = $cn->query($sqlSelect);
            $row = $result->fetch_array();

            $newPerson = $row;
        } else {

            $newPerson = $row;
        }

        return $newPerson;
    }


    //Pagos matriculas
    public function savePagoMatricula($person, $details)
    {

        if ($this->mode === 'production') {
            session_start();
            if (!isset($_SESSION['idusuario'])) {
                $this->response['success'] = false;
                $this->response['message'] = 'No autorizad(a)';
                $this->response['data'] = null;
                echo json_encode($this->response);
                exit;
            }
        }

        include 'cn.php';

        $cn->begin_transaction();
        try {

            $pagoPapeleta = $this->savePapeletaPagoMatricula($person, $cn);

            foreach ($details as $value) {
                $this->saveDetaPapeletaPagoMatricula($pagoPapeleta->idpadre, $value, $cn);
            }

            $this->response['success'] = true;
            $this->response['message'] = 'Pago regitrado con exito';
            $this->response['data'] = $pagoPapeleta;

            $cn->commit();
            $cn->close();

            echo json_encode($this->response);
        } catch (\Throwable $th) {
            $this->response['success'] = false;
            $this->response['message'] = $th;
            $this->response['data'] = null;
            $cn->rollback();
            $cn->close();
            echo json_encode($this->response);
        }
    }

    public function savePapeletaPagoMatricula($person, $cn)
    {


        if ($this->mode === 'production') {
            session_start();
            $var_anio = $_SESSION['anio'];
            $idusuario = $_SESSION['idusuario'];
        } else {
            $var_anio =  '2023';
            $idusuario =  '0028';
        }

        $fecha     = date('Y-m-d'); //default: fecha actual

        $newPerson =  $this->saveEstudiante($person);


        $idtipo    = 0; //default: Estudiante
        $idcodigo =  $person->codigo_ingreso;
        $codigo    = $person->codigo_ingreso;
        $nombre    = $person->nombres  .' '. $person->primer_apellido . '  '.  $person->segundo_apellido ;
        $clave = $this->generar_clave();
        $obs    = ""; //default: sin observaciones

        $ip = $_SERVER['REMOTE_ADDR'];

        $sql  = "select teso_caja.serie from teso_usuariocaja left join teso_caja on teso_caja.idcaja=teso_usuariocaja.idcaja where teso_usuariocaja.idusuario ='$idusuario'";
        $result = $cn->query($sql);
        $row = $result->fetch_array();
        $serie = $row['serie'];

        # Generamos el numero
        $sql = "select max(numero) as maxnum from teso_papeletapago where anio='$var_anio' and serie='$serie'";
        $result = $cn->query($sql);
        $row = $result->fetch_array();
        $numero = str_pad($row['maxnum'] + 1, 6, '0', STR_PAD_LEFT);

        $sql = "insert into teso_papeletapago (anio,serie,numero,fecha,obs,hora,estado,clave,tipo,idcodigo,codigo,idusuario, nombre,ip) ";
        $sql .= "values ('$var_anio','$serie','$numero','$fecha','$obs',CURRENT_TIMESTAMP,'0','$clave','$idtipo','$idcodigo','$codigo','$idusuario', '$nombre','$ip') ";

        $cn->query($sql);

        $sql = "select max(idpapeleta) as maxid from teso_papeletapago";
        $result = $cn->query($sql);
        $row = $result->fetch_array();
        $newid = $row['maxid'];

        $datos = (object) [
            'idpadre' => $newid,
            'serie' => $serie,
            'numero' => $numero,
            'clave' => $clave,
            'error' => $cn->error,
            'newPerson' => $newPerson,
        ];

        return $datos;
    }

    public function saveDetaPapeletaPagoMatricula($papeleta, $detail, $cn)
    {

        $idpadre = $papeleta;
        $idtarifa = $detail->value;
        $cantidad = 1; //Default: 1
        $precio = $detail->price;
        $detalle = $detail->detail;


        $sql = "insert into teso_papeletatarifas (idpapeleta,idtarifa,cantidad,precio,detalle) ";
        $sql .= "values ('$idpadre','$idtarifa','$cantidad','$precio','$detalle') ";
        $cn->query($sql);

        $sql = "select IF(SUM(round(cantidad*precio,2)) IS NULL,0.00,SUM(round(cantidad*precio,2))) total from teso_papeletatarifas where idpapeleta='$idpadre'";
        $result = $cn->query($sql);
        $row = $result->fetch_array();
        $importe = $row['total'];

        $sql = "update teso_papeletapago set ";
        $sql .= "total='$importe' ";
        $sql .= "where idpapeleta='$idpadre'";
        $cn->query($sql);
    }

    function saveEstudiante($person)
    {

        include 'unap.php';
        $newPerson = null;
        $sqlSelect = "select num_mat from estudiante ";
        $sqlSelect .= "where num_mat = '$person->codigo_ingreso';";
        $result = $unap->query($sqlSelect);
        $row = $result->fetch_array();

        if ($row === null) {
            $codigo = $person->codigo_ingreso;
            $nombres =  strtoupper($person->nombres);
            $paterno  = strtoupper($person->primer_apellido);
            $materno  = strtoupper($person->segundo_apellido);

            $sql = "insert into estudiante (num_mat,paterno,materno,nombres) ";
            $sql .= "values ('$codigo','$paterno','$materno','$nombres') ";
            $unap->query($sql);

            $sqlSelect = "select num_mat from estudiante ";
            $sqlSelect .= "where num_mat = '$person->codigo_ingreso';";
            $result = $unap->query($sqlSelect);
            $row = $result->fetch_array();

            $newPerson = $row;
        } else {

            $newPerson = $row;
        }
        $unap->close();
        return $newPerson;
    }

    function getConceptos($term, $cn)
    {
        $query = `SELECT idtarifa, codigo, destarifa
                 FROM teso_tarifascaja
                 WHERE destarifa LIKE '%$term%';`;

        $result = $cn->query($query);
        $res = $result->fetch_assoc(MYSQLI_ASSOC);

        $cn->close();

        if (count($res) > 0) {
            $this->response['success'] = false;
            $this->response['message'] = 'Otra persona NO encontrad(a)';
            $this->response['data'] = null;
            echo json_encode($this->response);
        } else {
            $this->response['success'] = true;
            $this->response['message'] = 'Otra persona encontrad(a)';
            $this->response['data'] = $res;
            echo json_encode($this->response);
        }
    }

    protected function generar_clave()
    {
        $caracteres = "abcdefghijklmnopqrstuvwxyz1234567890"; //posibles caracteres a usar
        $numerodeletras = 20; //numero de letras para generar el texto
        $cadena = ""; //variable para almacenar la cadena generada
        for ($i = 0; $i < $numerodeletras; $i++) {
            $cadena .= substr($caracteres, rand(0, strlen($caracteres)), 1); /*Extraemos 1 caracter de los caracteres
        entre el rango 0 a Numero de letras que tiene la cadena */
        }
        return $cadena;
    }
}

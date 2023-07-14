<?php
defined('BASEPATH') or exit('No direct script access allowed');
class CORE
{
    protected $response;
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET,POST");
        header("Access-Control-Allow-Headers: Content-Type");

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

    public function getDataAdmition()
    {
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer 8|eQ5sFCCQzTpCKP9nXS9rYpzeaku0tF7ib2iNbglb'
        );

        $url = 'https://inscripciones.admision.unap.edu.pe/api/get-postulante-pago/71576906/4';

        $options = array(
            'http' => array(
                'header' => implode("\r\n", $headers)
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response);

        $this->response['success'] = $data->status;
        $this->response['message'] = $data->mensaje;
        $this->response['data'] = $data->data;
        echo json_encode($this->response);
    }

    public function savePapeletaPago()
    {
    }

    public function _getDataAdmition()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://inscripciones.admision.unap.edu.pe/api/get-postulante-pago/71576906/4',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer 8|eQ5sFCCQzTpCKP9nXS9rYpzeaku0tF7ib2iNbglb'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $this->response['success'] = true;
        $this->response['message'] = 'Operación realizada con éxito';
        $this->response['data'] = $response;
    }
}

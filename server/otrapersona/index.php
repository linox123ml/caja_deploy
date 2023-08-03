<?php
define('BASEPATH', true);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../core/core.php';

$term = $_GET['term'];

$core = new CORE();

$core->getOtraPersona($term);
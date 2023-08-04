<?php
define('BASEPATH', true);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
require_once '../core/core.php';
require_once '../core/cn.php';

$term = $_GET['term'];
$core = new CORE();
$core->getConceptos($term, $cn);
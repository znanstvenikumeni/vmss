<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    header('Access-Control-Allow-Origin: *');
    die();
}
header('Access-Control-Allow-Origin: *');
include 'vmss/vmss.php';
$vmss = new vmssCore;
include 'vmss/router.php';

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('vmss_version', '1.0_dev_prerelease');
define('api_version', '1.0_dev_prerelease');
include '../vendor/autoload.php';
new \vmss\API\APIBootHandler();
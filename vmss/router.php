<?php
$request = $_SERVER['REQUEST_URI'];
$request = substr($request, 1); // removing the first url slash to explode the array properly
$routeSegments = explode("/", $request);
//var_dump($routeSegments);

switch ($routeSegments[0]) {
	case 'auth':
		$nonce = $vmss->tokenAuth($routeSegments[1], $routeSegments[2]);
		if($nonce){
			$response['nonce'] = $nonce;
			echo json_encode($response);
		}
		else{
			$vmss->handleAccessError();
		}
		break;
	
	default:
		$vmss->handleAccessError();
		break;
}
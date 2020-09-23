<?php
$request = $_SERVER['REQUEST_URI'];
$request = substr($request, 1); // removing the first url slash to explode the array properly
$routeSegments = explode("/", $request);

switch ($routeSegments[0]) {

	case 'video':
		echo json_encode($vmss->getVideoData($routeSegments[1]));
	break;
	case 'queue':
		if($routeSegments[1] !== $vmss->readConfig()->queueKey){
			die();
		}
		$vmss->handleQueueAction();
	break;
	case 'regenerateThumbs':
		if($routeSegments[1] !== $vmss->readConfig()->queueKey) die();
		$vmss->regenerateThumbnails();
	break;
	break;
	case 'markAsDone':
		if($routeSegments[6] !== $vmss->readConfig()->queueKey){
			die();
		}
		$vmss->popFromQueue($routeSegments[1], $routeSegments[2], $routeSegments[3], $routeSegments[4], $routeSegments[5]);
	break;
	case 'auth':
		$nonce = $vmss->tokenAuth($routeSegments[1], $routeSegments[2]);

		if($nonce){
			if(isset($routeSegments[3])){
				if($routeSegments[3] == 'auto'){
					header('Location: /upload/'.$routeSegments[1].'/'.$nonce.'');
					die();
				}
				if($routeSegments[3] == 'url'){
					echo '/upload/'.$routeSegments[1].'/'.$nonce;
					die();
				}
			}
			
			$response['nonce'] = $nonce;
			$response = json_encode($response);
			echo $response;
		}
		else{
			$vmss->handleAccessError();
		}
		break;
	case 'upload':
		if($vmss->nonceAuth($routeSegments[1], $routeSegments[2])){
			$files = $_FILES['files'];
			if($files['error'][0]){
				http_response_code(400);
				$vmss->handleError(new Exception('FailedUploadException: '.$files['error'][0]));
			}
			$file_path = $files['tmp_name'][0]; 
			$file_name = $_POST['name'];
			$originals['name'] = $_POST['name'];
			$fileNaming = explode('.', $file_name);
			$fileExtension = $fileNaming[array_key_last($fileNaming)];
			$config = $vmss->readConfig();
			$allowedExtensions = (array)$config->allowedTypes;
			if(!in_array(strtolower($fileExtension), $allowedExtensions)){
				http_response_code(400);
				$vmss->handleError('Filetype not allowed');
			}
			$file_name = bin2hex(openssl_random_pseudo_bytes(32)).md5($file_name);
			if(move_uploaded_file($file_path, 'uploads/' . basename($file_name))){
				$vmssID = bin2hex(openssl_random_pseudo_bytes(64));
				$vmss->saveFile($vmssID, $file_name, $routeSegments[1]);
				$response['upload'] = 'success';
				$response['id'] = $vmssID;
				echo json_encode($response);
			}
			else{
				http_response_code(400);
				$vmss->handleError(new Exception('FailedUploadProcessingException'));
			}
		}
		else{
			$vmss->handleAccessError();
		}
		
	break;
	default:
		$vmss->handleAccessError();
		break;
}

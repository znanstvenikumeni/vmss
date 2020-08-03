<?php


namespace vmss\Video;


use vmss\Server\Error;

class UploadHandler
{
    public function upload(){
        $files = $_FILES['files'];
        if($files['error'][0]){
           new Error('Failed upload with error: '.$files['error'][0], 'upload', 400, 'badRequestError');
        }
        $file_path = $files['tmp_name'][0];
        $file_name = $_POST['name'];
        $originals['name'] = $_POST['name'];
        $fileNaming = explode('.', $file_name);
        $fileExtension = $fileNaming[array_key_last($fileNaming)];
        $Config = new \vmss\Server\Config();
        $allowedExtensions = (array)$Config->allowedTypes;
        $fileExtension = strtolower($fileExtension);
        if(!in_array($fileExtension, $allowedExtensions)){
            new Error('Extension not allowed', 'upload', 400, 'badRequestError');
        }
        $file_name = bin2hex(openssl_random_pseudo_bytes($Config->randomStringLength)).md5($file_name);
        $Destination = __DIR__.'/../../'.$Config->uploadFolder;
        if(move_uploaded_file($file_path, $Destination . basename($file_name))){
            $VideoObject = new vmssVideoObject();
            $VideoObject->originalUploadFile = $file_name;
            $VideoObject->new();
            $response['upload'] = 'success';
            $response['id'] = $VideoObject->vmssID;
            return json_encode($response);
        }
        else{
            new Error('Couldn\'t upload', 'upload', 500, 'internalServerError');

        }
    }
}
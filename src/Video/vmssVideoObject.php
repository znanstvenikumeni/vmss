<?php


namespace vmss\Video;


use vmss\Queue\MySQLQueueHandler;
use vmss\Server\Error;

class vmssVideoObject
{
    public $vmssID;
    public $originalUploadFile;
    public $data;
    public $files;
    public $clientKey;

    public function __toString(){
        $ResponseVideo['vmssID'] = $this->vmssID;
        $ResponseVideo['data'] = $this->data;
        $ResponseVideo['files'] = $this->files;
        $Response['video'] = $ResponseVideo;
        $Resp = json_encode($Response);
        return $Resp;
    }
    public function new(){
        $this->generatevmssID();
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $SQLQuery = "INSERT INTO videos VALUES (null, :vmssID, :originalUploadFile, :data, :files, :clientKey)";
        $Params['vmssID'] = $this->vmssID;
        $Params['originalUploadFile'] = $this->originalUploadFile;
        $Params['data'] = $this->data;
        $Params['files'] = $this->files;
        $Params['clientKey'] = $this->clientKey;

        $Statement = $PDO->prepare($SQLQuery);
        try {
            $Statement->execute($Params);
        }
        catch(\Exception $e){
            $Return['result'] = 'error';
            $Return['exception'] = $e;
            return $Return;
        }
        $this->enqueueSelf();
        return $this->vmssID;
    }

    public function fetch(){
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $SQLQuery = "SELECT * FROM videos WHERE vmssID = :vmssID";
        $Params['vmssID'] = $this->vmssID;
        $Statement = $PDO->prepare($SQLQuery);

        try {
            $Res = $Statement->execute($Params);
            $ResultSet = $Statement->fetch(\PDO::FETCH_ASSOC);

        }
        catch(\Exception $e){
            new Error('Internal vmss error', 'vmss\Video\vmssVideoObject');
        }
        if(!$ResultSet) new Error('No video with that vmssID', '/video', 404, 'notFoundError');
        $this->originalUploadFile = $ResultSet['originalUploadFile'];
        $this->data = $ResultSet['data'];
        $this->files = $ResultSet['files'];
        $this->clientKey = $ResultSet['clientKey'];
    }

    public function update(){
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $SQLQuery = "UPDATE video SET originalUploadFile = :originalUploadFile, data = :data, files = :files, clientKey = :clientKey WHERE vmssID = :vmssID";
        $Params['vmssID'] = $this->vmssID;
        $Params['originalUploadFile'] = $this->originalUploadFile;
        $Params['data'] = $this->data;
        $Params['files'] = $this->files;
        $Params['clientKey'] = $this->clientKey;

        $Statement = $PDO->prepare($SQLQuery);
        try {
            $Statement->execute($Params);
        }
        catch(\Exception $e){
            $Return['result'] = 'error';
            $Return['exception'] = $e;
            return $Return;
        }
        return $this->vmssID;
    }
    private function enqueueSelf(){
        $Config = new \vmss\Server\Config();
        $Queue = new \vmss\Queue\MySQLQueueHandler();
        foreach($Config->queueActions as $action){
            $Queue->enqueue($this->vmssID, $action);
        }
    }
    private function generatevmssID(){
        $Config = new \vmss\Server\Config();
        $this->vmssID = bin2hex(openssl_random_pseudo_bytes($Config->randomStringLength));
    }

}
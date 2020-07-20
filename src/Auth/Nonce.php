<?php


namespace vmss\Auth;


class Nonce
{
    public $app;
    public $nonce;
    public $used;
    public $timeIssued;
    public function __construct($app){
        $Config = new \vmss\Server\Config();
        $this->nonce = bin2hex(openssl_random_pseudo_bytes($Config->randomStringLength));
        $this->timeIssued = time();
        $this->used = false;
        $this->app = $app;
    }
    public function add(){
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $SQLQuery = "INSERT INTO nonces VALUES (null, :app, :time, :nonce, :used)";
        $Params['app'] = $this->app;
        $Params['time'] = $this->timeIssued;
        $Params['nonce'] = $this->nonce;
        $Params['used'] = $this->used;
        $Statement = $PDO->prepare($SQLQuery);
        try {
            $Statement->execute($Params);
        }
        catch(\Exception $e){
            $Return['result'] = 'error';
            $Return['exception'] = $e;
            return $Return;
        }
        $Return['result'] = 'success';
        $Return['nonce'] = $this->nonce;
        return $Return;
    }
    public function used(){
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $SQLQuery = "UPDATE nonces SET used = true WHERE nonce = :nonce";
        $Params['nonce'] = $this->nonce;
        $Statement = $PDO->prepare($SQLQuery);
        try {
            $Statement->execute($Params);
        }
        catch(\Exception $e){
            $Return['result'] = 'error';
            $Return['exception'] = $e;
            return $Return;
        }
        $Return['result'] = 'success';
        $Return['nonce'] = $this->nonce;
        return $Return;
    }
    public function fetch(){
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $SQLQuery = "SELECT FROM nonces WHERE nonce = :nonce";
        $Params['nonce'] = $this->nonce;
        $Statement = $PDO->prepare($SQLQuery);
        $Statement->execute($Params);
        $ResultSet = $Statement->fetch(\PDO::FETCH_ASSOC);
        $this->used = $ResultSet['used'];
        $this->app = $ResultSet['app'];
        $this->timeIssued = $ResultSet['time'];
    }
}
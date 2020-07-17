<?php


namespace vmss\Client;


use vmss\Server\Config;

class Client
{
    public $id;
    public $name;
    public $publicKey;
    protected $privateKeyHash;
    public function __construct($id, $name, $publicKey, $privateKeyHash){
        $this->id = $id;
        $this->name = $name;
        $this->publicKey = $publicKey;
        $this->privateKeyHash = $privateKeyHash;
    }
    public function verifyPrivateKey($key){
        if(password_verify($key, $this->privateKeyHash)) return true;
        else return false;
    }

    public function changePrivateKey($key){
        $Config = new Config();
        $this->privateKeyHash = password_hash($key, $Config->privateKeyAlgorithm);
    }
}
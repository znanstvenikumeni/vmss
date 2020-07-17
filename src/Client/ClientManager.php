<?php


namespace vmss\Client;


class ClientManager
{
    public function addClient($name){
        $Config = new Config();
        $Client = new Client(-1, $name, bin2hex(openssl_random_pseudo_bytes($Config->randomStringLength)),)
    }
}
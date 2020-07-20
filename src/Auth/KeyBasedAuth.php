<?php


namespace vmss\Auth;



class KeyBasedAuth
{
    private $app;
    public function auth($publicKey, $secretKey){
        $ClientManager = new \vmss\Client\ClientManager();
        $Client = $ClientManager->searchByPublicKey($publicKey);
        if(!($Client->id ?? 0)) return false;
        $this->app = $Client->name;
        return $Client->verifyPrivateKey($secretKey);
    }
    public function app(){
        return $this->app ?? null;
    }
}
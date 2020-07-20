<?php


namespace vmss\Auth;


class NonceBasedAuth
{
    public $app;
    public function generateNonce(){
        $Nonce = new Nonce($this->app);
        $Nonce->add();
        return $Nonce->nonce;
    }
    public function validateNonce($nonce){
        $Nonce = new Nonce($this->app);
        $Nonce->nonce = $nonce;
        $Nonce->fetch();
        if($Nonce->used) return false;
        $Config = new \vmss\Server\Config();
        $timeLimit = $Config->autoExpireNoncesAfter;
        if($timeLimit != -1 && time() - $Nonce->timeIssued > $timeLimit) return false;
        $Nonce->used();
        return true;
    }
}
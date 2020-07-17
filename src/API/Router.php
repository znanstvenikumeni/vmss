<?php


namespace vmss\API;


class Router
{
    private $Endpoint;
    private $Method;
    private $RequestRoutes;
    private $Routes;
    public function __construct(){
        $this->Routes = [];
    }
    public function route($Method, $Endpoint, $RequestRoutes){
        $this->Method = $Method;
        $this->Endpoint = $Endpoint ?? '/';
        if(!$this->Endpoint) $this->Endpoint = '/';
        $this->RequestRoutes = $RequestRoutes;
        foreach($this->Routes as $Route){

            if($Route->getMethod() == $this->Method && $this->Endpoint == $Route->getEndpoint()) $Route->execute();
        }
    }
    public function addRoute(\vmss\API\Route $Route){
        $this->Routes[] = $Route;


    }


}
<?php


namespace vmss\API;


class Router
{
    private $Endpoint;
    private $Method;
    public $RequestRoutes;
    private $Routes;
    public function __construct(){
        $this->Routes = [];
    }
    public function route($Method, $Endpoint, $RequestRoutes){
        $this->Method = $Method;
        $this->Endpoint = $Endpoint ?? '/';
        if(!$this->Endpoint) $this->Endpoint = '/';
        $this->RequestRoutes = $RequestRoutes;
        $Found = false;
        foreach($this->Routes as $Route){
            if($Route->getMethod() == $this->Method && $this->Endpoint == $Route->getEndpoint()){
                $Route->execute();
                $Found = true;
            }
        }
        if(!$Found) {
            header('Location: /404');
        }
    }
    public function addRoute(\vmss\API\Route $Route){
        $this->Routes[] = $Route;


    }


}
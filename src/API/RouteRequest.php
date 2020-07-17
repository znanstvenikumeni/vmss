<?php


namespace vmss\API;


class RouteRequest
{
    public $Method;
    public $Request;
    public $Routes;
    public $Endpoint;

    public function __construct($Request, $Method){
        $this->Request = substr($Request, 1);
        $this->Routes = explode('/', $this->Request);
        $this->Endpoint = $this->Routes[0];
        $this->Method = $Method;
    }
    public function execute($Router){
        $Router->route($this->Method, $this->Endpoint, $this->Routes);
    }

}
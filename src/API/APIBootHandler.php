<?php

namespace vmss\API;

class APIBootHandler
{
    private $Router;
    public $RouteRequest;
    public function __construct()
    {
        $this->createRouteRequest();
        $this->Router = $this->constructRouter();
        $this->RouteRequest->execute($this->Router);

    }
    private function createRouteRequest()
    {
        $Request = $_SERVER['REQUEST_URI'];
        $Method = $_SERVER['REQUEST_METHOD'];
        $this->RouteRequest = new RouteRequest($Request, $Method);
    }
    private function constructRouter(){
        $Router = new Router();
        include 'RouteDefinitions.php';
        return $Router;
    }
}
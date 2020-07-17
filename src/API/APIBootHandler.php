<?php

namespace vmss\API;

class APIBootHandler
{
    private $Router;
    public function __construct()
    {
        $this->Router = $this->constructRouter();
        $this->createRouteRequest();
    }
    private function createRouteRequest()
    {
        $Request = $_SERVER['REQUEST_URI'];
        $Method = $_SERVER['REQUEST_METHOD'];
        $RouteRequest = new RouteRequest($Request, $Method);
        $RouteRequest->execute($this->Router);
    }
    private function constructRouter(){
        $Router = new Router();
        include 'RouteDefinitions.php';
        return $Router;
    }
}
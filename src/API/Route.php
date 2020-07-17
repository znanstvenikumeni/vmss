<?php
namespace vmss\API;
class Route
{
    protected $Method;
    protected $Endpoint;
    protected $MinVersion;
    protected $MaxVersion;
    protected $FunctionToCall;
    public function __construct($Method, $Endpoint, $FunctionToCall){
        $this->Method = $Method;
        $this->Endpoint = $Endpoint;
        $this->FunctionToCall = $FunctionToCall;
    }
    public function execute(){
        call_user_func($this->FunctionToCall);
    }
    public function setMinVersion($MinVersion){
        $this->MinVersion = $MinVersion;
    }
    public function setMaxVersion($MaxVersion){
        $this->MaxVersion = $MaxVersion;
    }
    public function getMethod(){
        return $this->Method;
    }
    public function getEndpoint(){
        return $this->Endpoint;
    }
    public function getMinVersion(){
        return $this->MinVersion;
    }
    public function getMaxVersion(){
        return $this->MaxVersion;
    }
    public function getFunctionToCall(){
        return $this->FunctionToCall;
    }
}
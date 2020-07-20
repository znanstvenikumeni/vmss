<?php


namespace vmss\Server;


class Config
{
    private $ConfigFilePath = __DIR__.'/../../.config.json';
    private $Config;
    public function __construct($ConfigFilePath = __DIR__.'/../../.config.json')
    {
        $this->ConfigFilePath = $ConfigFilePath;
        $ConfigFile = file_get_contents($this->ConfigFilePath);
        $this->Config = json_decode($ConfigFile);
    }
    public function __get($name){
        if(isset($this->Config->$name)) return $this->Config->$name;
        else throw new \Exception('Config property '.$name.' not set in ConfigFile');
    }
    public function __set($name, $value){
        $this->Config->$name = $value;
        $this->updateConfig();
    }
    private function updateConfig(){
        $Config = json_encode($this->Config);
        file_put_contents($this->ConfigFilePath, $Config);
    }
}
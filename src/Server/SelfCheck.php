<?php


namespace vmss\Server;


class SelfCheck
{
    public function perform($type = 'FULL'){
        try{
            $Results['canReadConfigAndEnvironmentSet'] = $this->canReadConfig();
        }
        catch(\Exception $e){
            $Results['canReadConfigAndEnvironmentSet'] = false;
            $Results['canReadConfigAndEnvironmentSetException'] = $e;
        }
        try{
            $Results['canConnectToDatabaseViaPDO'] = $this->canConnectToDatabaseViaPDO();
        }
        catch(\Exception $e){
            $Results['canConnectToDatabaseViaPDO'] = false;
            $Results['canConnectToDatabaseViaPDOException'] = $e;
        }
        return $Results;

    }
    private function canReadConfig(){
        $Config = new \vmss\Server\Config();
        if($Config->env ?? null) return true;
        return false;
    }
    private function canConnectToDatabaseViaPDO(){
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        return ($PDO->getAttribute(\PDO::ATTR_CONNECTION_STATUS) != NULL);
    }
}
<?php


namespace vmss\Server;


class PDOConnectionFactory
{
    public function connect(){
        $Config = new Config();
        $PDO = new PDOConnection($Config->database->user, $Config->database->password, $Config->database->host, $Config->database->type, $Config->database->database);
        return $PDO->getDBObject();
    }

}
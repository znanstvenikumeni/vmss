<?php


namespace vmss\Server;


interface DatabaseConnection
{

    public function __construct($user, $password, $host, $type, $database);
    public function getDBObject();
}
<?php


namespace vmss\Server;


interface DatabaseConnection
{
    protected $user;
    protected $password;
    protected $host;
    protected $type;
    protected $database;

    public function __construct($user, $password, $host, $type, $database);
    public function getDBObject();
}
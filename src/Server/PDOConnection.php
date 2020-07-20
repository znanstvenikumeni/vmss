<?php


namespace vmss\Server;


class PDOConnection implements DatabaseConnection
{
    protected $user;
    protected $password;
    protected $host;
    protected $type;
    protected $database;
    protected $PDO;

    public function __construct($user, $password, $host, $type, $database){
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->type = $type;
        $this->database = $database;
        $this->PDOConstructor();
    }
    public function getDBObject(){
        return $this->PDO;
    }
    private function PDOConstructor(){
        $dsn = $this->type.':dbname='.$this->database.';host='.$this->host;
        $PDO = new \PDO($dsn, $this->user, $this->password);
        $this->PDO = $PDO;
    }

}
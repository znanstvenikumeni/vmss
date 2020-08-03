<?php


namespace vmss\Queue;


class MySQLQueueHandler implements QueueHandlerInterface
{
    public function enqueue($vmssID, $action, $status = 0){
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $SQLQuery = "INSERT INTO queue VALUES (null, :vmssID, :action, :status)";
        $Params['vmssID'] = $vmssID;
        $Params['action'] = $action;
        $Params['status'] = $status;
        $Statement = $PDO->prepare($SQLQuery);
        try {
            $Statement->execute($Params);
        }
        catch(\Exception $e){
            $Return['result'] = 'error';
            $Return['exception'] = $e;
            return $Return;
        }
        return $PDO->lastInsertId();
    }
    public function change($id, $status){
        $PDOFactory = new \vmss\Server\PDOConnectionFactory();
        $PDO = $PDOFactory->connect();
        $SQLQuery = "UPDATE queue SET status = :status WHERE id=:id";
        $Params['id'] = $id;
        $Params['status'] = $status;
        $Statement = $PDO->prepare($SQLQuery);
        try {
            $Statement->execute($Params);
        }
        catch(\Exception $e){
            $Return['result'] = 'error';
            $Return['exception'] = $e;
            return $Return;
        }
    }
}
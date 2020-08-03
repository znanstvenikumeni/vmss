<?php


namespace vmss\Queue;


interface QueueHandlerInterface
{
    public function enqueue($vmssID, $action, $status = 0);
    public function change($id, $status);
}
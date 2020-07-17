<?php


namespace vmss\Server;


class Error
{
    public $type;
    public $responseCode;
    public $message;
    public $context;

    public function __construct($message, $context, $responseCode = 500, $type = 'internalError')
    {
        $this->message = $message;
        $this->context = $context;
        $this->responseCode = $responseCode;
        $this->type = $type;
        if($this->type != 'recoverableError' && $this->type != 'warning'){
            $ErrorOutput['error']['type'] = $this->type;
            $ErrorOutput['error']['message'] = $this->message;
            $Output = json_encode($ErrorOutput);
            http_response_code($this->responseCode);
            die($Output);
        }
    }
}
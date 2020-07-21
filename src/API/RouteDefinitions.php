<?php
$Router->addRoute( new vmss\API\Route('GET', '/', function(){
    $Config = new \vmss\Server\Config();
    if($Config->showWelcome) include __DIR__.'/../../docs/Frontpage.php';
}) );
$Router->addRoute( new vmss\API\Route('GET', '404', function(){
    new \vmss\Server\Error('No such endpoint available on this API version ('.api_version.')', 'router', 404, 'notFoundError');
}));
$Router->addRoute(new vmss\API\Route('GET', 'addClient', function(){
    /**$ClientManager = new vmss\Client\ClientManager();
    $Output = $ClientManager->addClient($this->RouteRequest->Routes[1]);
    var_dump($Output);**/
}));
$Router->addRoute(new \vmss\API\Route('GET', 'auth', function(){
    $Config = new \vmss\Server\Config();
    $KeyBasedAuth = new \vmss\Auth\KeyBasedAuth();
    if(!isset($this->RouteRequest->Routes[1]) || !isset($this->RouteRequest->Routes[2])) new \vmss\Server\Error('Endpoint arguments missing', 'auth', 400, 'badRequestError');
    $Auth = $KeyBasedAuth->auth($this->RouteRequest->Routes[1], $this->RouteRequest->Routes[2]);
    if(!$Auth) new \vmss\Server\Error('Access forbidden', 'KeyBasedAuth', 403, 'privilegeError');
    $Nonce = new \vmss\Auth\Nonce($KeyBasedAuth->app());
    $Return = $Nonce->add();
    if(!($this->RouteRequest->Routes[3] ?? '')) echo json_encode($Return);
    else if($this->RouteRequest->Routes[3] == 'auto') header('Location: /upload/'.$this->RouteRequest->Routes[1].'/'.$Return['nonce']);
    else if($this->RouteRequest->Routes[3] == 'url') echo $Config->urlbase.'/upload/'.$this->RouteRequest->Routes[1].'/'.$Return['nonce'];
    else new \vmss\Server\Error('No such flag available on this API version ('.api_version.')', 'auth', 400, 'badRequestError');
}));
$Router->addRoute(new \vmss\API\Route('POST', 'upload', function(){
    $UploadHandler = new \vmss\Video\UploadHandler();
    $UploadHandler->upload();
}));
$Router->addRoute(new \vmss\API\Route('GET', 'selfcheck', function(){
    $Selfcheck = new \vmss\Server\SelfCheck();
    $Results = $Selfcheck->perform();
    if(!isset($this->RouteRequest->Routes[1])) echo json_encode($Results);
}));
$Router->addRoute(new \vmss\API\Route('GET', 'test', function(){
    $Config = new \vmss\Server\Config();
    if($Config->env == 'dev') include __DIR__.'/../../docs/ImplementationSample.php';
}));
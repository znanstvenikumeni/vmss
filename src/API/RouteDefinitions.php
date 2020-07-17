<?php
$Router->addRoute( new vmss\API\Route('GET', '/', function(){
    echo 'babababa';
}) );
$Router->addRoute( new vmss\API\Route('GET', '/404', function(){
    echo '404';
}));
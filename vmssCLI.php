#!/usr/bin/php
<?php
$configurationFile = file_get_contents('.config.json');
$config = json_decode($configurationFile);
$vmssver = '0.0-dev';
$vmsscliver = '0.0-dev';
if($argc == 1){
	echo 'vmss management tools                     ';
	echo "\n";
	echo '------------------------------------------';
	echo "\n";
	echo 'usage: php vmssCLI.php [tool] [options]';
	echo "\n";
	echo '    or ./vmssCLI.php [tool] [options]';
	echo "\n";
	echo 'see ./vmssCLI.php help for available tools';
	echo "\n";
}
if(!isset($argv[1])) die();
if($argv[1] == 'help'){
	echo 'vmss release '.$vmssver.' - vmsscli release '.$vmsscliver; 
	echo "\n";
	echo 'available tools:';
	echo "\n";
	echo '=================';
	echo "\n";
	echo 'generateSecret';
	echo "\n";
	echo '--------------';
	echo "\n";
	echo 'The generateSecret tool generates a secret token for your app and a hash for you to store in the database.';
	echo 'This tool requires PHP 7.3+';
	echo "\n";
	echo 'Options: (not available)';
	echo "\n";
	echo '=================';
	echo "\n";
	echo 'about';
	echo "\n";
	echo '-----';
	echo "\n";
	echo 'The about tool gives you info about your vmss installation, the licenses and general information about the vmss project';
	echo 'Options: (not available)';
	echo "\n";
}
if($argv[1] == 'about'){
	echo 'vmss release '.$vmssver.' - vmsscli release '.$vmsscliver; 
	echo "\n";
	echo 'code / new releases: github.com/znanstvenikumeni/vmss';
	echo "\n";
}
if($argv[1] == 'generateSecret'){
	$secret = bin2hex(openssl_random_pseudo_bytes(256));
	$hash = password_hash($secret, $config->hash);
	echo 'the secret key for use in your app: ';
	echo "\n";
	echo $secret;
	echo "\n";
	echo 'the hash for database storage: ';
	echo "\n";
	echo $hash;
	echo "\n";
}
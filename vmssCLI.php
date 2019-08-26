#!/usr/bin/php
<?php
$configurationFile = file_get_contents('.config.json');
$config = json_decode($configurationFile);
$vmssver = '0.2-b1';
$vmsscliver = '0.2-b1';
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
if($argv[1] == 'queue'){
	include 'vmss/vmss.php';
	$vmss = new vmssCore;
	$vmss->handleQueueAction();
}
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
	echo '=================';
	echo "\n";
	echo 'queue';
	echo "\n";
	echo '-----';
	echo "\n";
	echo 'The queue tool handles *all* queue actions that haven\'t been marked as done.';
	echo "\n";
	echo 'All actions will execute procedurally (in other words, in a sequential fashion) until there are no more remaining tasks in the queue.';
	echo "\n";
	echo 'We aim to improve this tool significantly in the future. Introduced in vmss0.2-b1';
	echo "\n";
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
#!/usr/bin/php
<?php
$configurationFile = file_get_contents('.config.json');
$config = json_decode($configurationFile);

$vmssver = '0.2-b1';
$vmsscliver = '0.2-b1';

include 'vmss/vmss.php';
$vmss = new vmssCore();

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
	echo '=================';
	echo "\n";

	echo 'add';
	echo "\n";
	echo '-----';
	echo "\n";

	echo "A tool for adding entries to vmss. Currently only supports clients.\n";
	echo "Usage: ./vmssCLI.php add client [name] [publicKey] [secretKey]\n";
	echo "\n";
	echo '=================';
	echo "\n";

	echo 'search';
	echo "\n";
	echo '-----';
	echo "\n";

	echo "A tool for searching vmss. Currently only supports videos.\n";
	echo "Usage: ./vmssCLI.php search videos [search]\n";
	echo "Options for the [search] parameter:\n";
	echo "* all\n";
	echo "use ./vmssCLI.php search videos all to list all videos in VMSS. This could take a significant amount of time on large installations\n";
	echo "* [query]\n";
	echo "construct a query in the [field]/[data] format\n";
	echo "for instance, to search vmssID for abcdef, use ./vmssCLI.php search videos vmssID/abcdef\n";
	echo "searchable fields: \n";
	echo "vmssID, originalUploadFile, data, files, clientKey";

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
if($argv[1] == 'add'){
	if($argv[2] == 'client'){
		$name = $argv[3];
		$publicKey = $argv[4];
		$secretKey = $argv[5];
		if(!$name || !$publicKey || !$secretKey){
			die('All arguments are mandatory.');
		}
		$sqlQuery = 'INSERT INTO allowedClients VALUES (null, :name, :publicKey, :secretKey)';
		try{
			$statement = $vmss->runDBQuery($sqlQuery, ['name' => $name, 'secretKey' => $secretKey, 'publicKey' => $publicKey]);
		}
		catch(Exception $e){
			echo "Couldn't add the client due to a MySQL exception.\n";
			die();
		}
		echo "***CLIENT ADDED***\n";
		echo "You've successfully added a client with the following data to VMSS: \n";
		echo "Name: ".$name."\n";
		echo "Public key: ".$publicKey."\n";
		echo "Secret key: ".$secretKey."\n";
	}
	if($argv[2] == 'video'){
		echo 'Please use the API to add videos.';
	}
	if($argv[2] == 'nonce'){
		echo 'Nonces can\'t be added manually.';
	}
}
if($argv[1] == 'search'){
	if($argv[2] == 'videos'){
		if($argv[3] == 'all'){
			$string = '%';
		}
		else{
			$string = $argv[3];
		}
		if($string != '%'){
			$Search = explode('/', $string);
			switch($Search[0]){
				case 'vmssID':
				case 'originalUploadFile':
				case 'data':
				case 'files':
				case 'originalUploadFile':
				case 'clientKey':
				$Param = $Search[0];
				break;
				default:
				die('Invalid search field');
				break;
			}
			$sqlQuery = 'SELECT * FROM videos WHERE '.$Param.' LIKE :query';
			$statement = $vmss->runDBQuery($sqlQuery, ['query' => $Search[1]]);


		}
		else{
			$sqlQuery = 'SELECT * FROM videos';
			$statement = $vmss->runDBQuery($sqlQuery);

		}
		$res = $statement->fetchAll(PDO::FETCH_ASSOC);
		echo var_export($res);
	}
}
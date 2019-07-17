<?php
/** vmss core functions **/
class vmssCore{

	/**
	Reads the .config.json file and returns it as a PHP object.
	@return object configuration data
	**/
	function readConfig(){
		$configurationFile = file_get_contents('.config.json');
		$config = json_decode($configurationFile);
		return $config;
	}
	
	/**
	Connects to the database using the .config.json configuration and returns a PDO object.
	@return object a PDO connection object.
	**/
	function databaseConnect(){
		$config = $this->readConfig();
		$databaseType = $config->database->type;
		$databaseHost = $config->database->host;
		$databaseUser = $config->database->user;
		$databasePassword = $config->database->password;
		$database = $config->database->database;
		$charset = $config->database->charset;
		$connectionString = $databaseType.':host='.$databaseHost.';dbname='.$database.';charset='.$charset;
		try {
			$connection = new PDO($connectionString, $databaseUser, $databasePassword);
		} catch (Exception $e) {
			$this->handleError($e);
		}
		
		return $connection;
	}
	/**
	A general function to handle database queries using PDO to only have one PDO connection during script execution.
	@params $query string SQL query
	@params $params array of parameters for prepared statements
	@return $statement object PDO object after execution
	**/
	function runDBQuery($query, $params = null){
		$connection = $this->databaseConnect();
		$statement = $connection->prepare($query);
		$statement->execute($params);
		return $statement;
	}
	/**
	Simply handles an access error showing a standard response for an access error and halting script execution.
	**/
	function handleAccessError(){
		include 'stdResponses/AccessError.php';
		die();
	}
	/**
	Handles errors depending on context and halts script execution. $e can be an exception or string.
	**/
	function handleError($e){
		$this->readConfig;
		$errorArray['error'] = 'internalError';
		if($config->env == 'dev'){
			$errorArray['data'] = $e;
		}
		json_encode($errorArray);
		echo $errorArray;
		die();
	}

	function tokenAuth($publicKey, $secretKey){
		$sqlQuery = 'SELECT * FROM allowedClients WHERE publicKey = :publicKey';
		$statement = $this->runDBQuery($sqlQuery, ['publicKey' => $publicKey]);
		$res = $statement->fetch();
		if(password_verify($secretKey, $res['secretKey'])){
			$nonce = bin2hex(openssl_random_pseudo_bytes(64));
			$nonceQuery = 'INSERT INTO nonces VALUES (null, "'.$res['name'].'",'.time().',"'.$nonce.'",0);'; // these are trusted, system generated values, so we're not escaping them to not cause any additional overhead
			$nonceStm = $this->runDBQuery($nonceQuery);
			return $nonce;
		}
		else{
			return null;
		}

	}	
}
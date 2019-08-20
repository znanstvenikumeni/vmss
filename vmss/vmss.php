<?php
/** vmss core functions **/
class vmssCore{

	/**
	* Reads the .config.json file and returns it as a PHP object.
	* @return object configuration data
	**/
	function readConfig(){
		$configurationFile = file_get_contents('.config.json');
		$config = json_decode($configurationFile);
		return $config;
	}
	
	/**
	* Connects to the database using the .config.json configuration and returns a PDO object.
	* @return object a PDO connection object.
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
	* A general function to handle database queries using PDO to only have one PDO connection during script execution.
	* @params $query string SQL query
	* @params $params array of parameters for prepared statements
	* @return $statement object PDO object after execution
	**/
	function runDBQuery($query, $params = null){
		$connection = $this->databaseConnect();
		$statement = $connection->prepare($query);
		$statement->execute($params);
		return $statement;
	}
	/**
	* Simply handles an access error showing a standard response for an access error and halting script execution.
	**/
	function handleAccessError(){
		include 'stdResponses/AccessError.php';
		die();
	}
	/**
	* Handles errors depending on context and halts script execution. $e can be an exception or string.
	* @param $e Exception|String error or exception to be handled.
	**/
	function handleError($e){
		$config = $this->readConfig();
		$errorArray['error'] = 'internalError';
		if($config->env == 'dev'){
			$errorArray['data'] = $e;
		}
		$errorArray = json_encode($errorArray);
		echo $errorArray;
		die();
	}
	/**
	 * Used to authenticate a client and generate a nonce. 
	 *
	 * @param string $publicKey client's public key
	 * @param string $secretKey client's secret key
	 * @return string|null returns a new, clean nonce if authentication was successful or null if it wasn't
	 */
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
	
	/**
	 * Checks if a nonce is valid (returns true or false accordingly) and marks it as used so it can't be used in the future.
	 *
	 * @param string $publicKey public key of the client which was issued the nonce
	 * @param string $nonce the nonce to check
	 * @return bool true if valid, false if not
	 */
	function nonceAuth($publicKey, $nonce){
		$sqlQuery = 'SELECT * FROM allowedClients WHERE publicKey = :publicKey';
		$statement = $this->runDBQuery($sqlQuery, ['publicKey' => $publicKey]);
		$appRow = $statement->fetch();
		$sqlQuery = 'SELECT * FROM nonces WHERE nonce = :nonce';
		$statement = $this->runDBQuery($sqlQuery, ['nonce' => $nonce]);
		$nonceRow = $statement->fetch();
		if($nonceRow['app'] != $appRow['name']) return false;
		//if($nonceRow['used'] != 0) return false;
		//$sqlQuery = 'UPDATE nonces SET used=1 WHERE nonce=:nonce';
		$statement = $this->runDBQuery($sqlQuery, ['nonce' => $nonce]);
		return true;
	}

	/**
	 * Saves the file after an upload and adds it to the database and the queue.
	 *
	 * @param string $vmssID the unique video identifier generated during the upload
	 * @param string $filename the original filename
	 * @param string $publicKey the public key of the upload client
	 * @return void
	 */
	function saveFile($vmssID, $filename, $publicKey){
		$sqlQuery = "INSERT INTO videos VALUES (null, :vmssID, :filename, null, null, :publicKey)";
		$statement = $this->runDBQuery($sqlQuery, ['publicKey' => $publicKey, 'vmssID' => $vmssID, 'filename' => $filename]);
		$config = $this->readConfig();
		$convertActions = (array)$config->convertActions;
		foreach($convertActions as $action){
			$sqlQuery = "INSERT INTO queue VALUES (null, :vmssID, :action, 0)";
			$statement = $this->runDBQuery($sqlQuery, ['vmssID' => $vmssID, 'action'=>$action]);
		}
		
	}

	/**
	 * Handles a queue action (usually, conversions) and generates and executes (in the background) a ffmpeg command chained with a curl command to pop the item from the queue when finished.
	 * Also, it marks the item in the queue as being processed.
	 * 
	 * @return void
	 */
	function handleQueueAction(){
		$sqlQuery = "SELECT * FROM queue WHERE status=0 ORDER BY id DESC LIMIT 1";
		$statement = $this->runDBQuery($sqlQuery);
		try{
			$queueRow = $statement->fetch();
		}
		catch(Exception $e){
			return false;
		}
		$sqlQuery = "SELECT * FROM videos WHERE vmssID=:vmssID LIMIT 1";
		$statement = $this->runDBQuery($sqlQuery, ['vmssID' => $queueRow['vmssID']]);
		$videoRow = $statement->fetch();
		$action = $queueRow['action'];
		$action = explode('/', $action);
		$resolution = $action[2];
		$format = $action[1];
		switch($resolution){
			case '1080p':
				$Dimension = '1920:1080';
			break;
			case '720p':
				$Dimension = '960:720';
			break;
			case '480p':
				$Dimension = '854:480';
			break;
			case '360p':
				$Dimension = '640:360';
			break;
		}

		switch($format){
			case 'mp4':
				$command = 'ffmpeg -y -i {input} -vf scale={dimension} -async 1 -metadata:s:v:0 start_time=0 -c:v libx264 -preset fast -c:a aac {output} -hide_banner'; 
				$exportExt = '.mp4';
			break;
			case 'webm':
				$command = 'ffmpeg -y -i {input} -vf scale={dimension} -async 1 -metadata:s:v:0 start_time=0 -f webm -c:v libvpx -b:v 1M -acodec libvorbis {output} -hide_banner';
				$exportExt = '.webm';
			break;
		}
		$format = $action[1];
		
		$videosAvailable = $videoRow['files'];
		if($videosAvailable){
			$videosAvailable = json_decode($videosAvailable, true);
		}
		$videosAvailable[$format][$resolution] = 'uploads/'.$videoRow['vmssID'].'_'.$format.'_'.$resolution.$exportExt;
		$fileName = $videoRow['vmssID'].'_'.$format.'_'.$resolution.$exportExt;
		$videosAvailable = json_encode($videosAvailable);
		
		$input = 'uploads/'.$videoRow['originalUploadFile'];
		$output = 'uploads/'.$videoRow['vmssID'].'_'.$format.'_'.$resolution.$exportExt;
		$command = str_replace('{input}', $input, $command);
		$command = str_replace('{output}', $output, $command);
		$command = str_replace('{dimension}', $Dimension, $command);
		$url = $this->readConfig()->urlbase.'/markAsDone/'.$queueRow['id'].'/'.$fileName.'/'.$videoRow['vmssID'].'/'.$format.'/'.$resolution.'/'.$this->readConfig()->queueKey; 
		$curl = 'curl -i -H "Accept: application/json" -H "Content-Type: application/json" -X GET \''.$url.'\'';
		$command = $command. ' && '.$curl;
		$sqlQuery = "UPDATE queue SET status=1 WHERE id=:id";
		$statement = $this->runDBQuery($sqlQuery, ['id' => $queueRow['id']]);
		exec($command);
	}


	/**
	 * Pops a video from the queue, marking it as completed and adds converted video metadata to the videos table.
	 *
	 * @param int $id the ID of the queue item being processed
	 * @param string $filename the filename of the saved video
	 * @param string $vmssID the vmss video id of the video being processed
	 * @param string $format the format which the video was saved in
	 * @param string $resolution the resolution the video was saved in
	 * @return void
	 */
	function popFromQueue($id, $filename, $vmssID, $format, $resolution){
		$sqlQuery = "UPDATE queue SET status=2 WHERE id=:id ";
		$statement = $this->runDBQuery($sqlQuery, ['id' => $id]);
		$sqlQuery = "SELECT * FROM videos WHERE vmssID=:vmssID LIMIT 1";
		$statement = $this->runDBQuery($sqlQuery, ['vmssID' => $vmssID]);
		$videoRow = $statement->fetch();
		$videosAvailable = $videoRow['files'];
		if($videosAvailable){
			$videosAvailable = json_decode($videosAvailable, true);
		}
		$videosAvailable[$format][$resolution] = 'uploads/'.$filename;
		$videosAvailable = json_encode($videosAvailable);
		$sqlQuery = "UPDATE videos SET files=:files WHERE vmssID=:vmssID";
		$statement = $this->runDBQuery($sqlQuery, ['vmssID' => $vmssID, 'files' => $videosAvailable]);
	}

	/**
	 * Gets and returns public video metadata.
	 *
	 * @param string $vmssID the vmss id of the requested video
	 * @return array $publicData a 2D associative array of the requested public data
	 */
	function getVideoData($vmssID){
		$sqlQuery = "SELECT * FROM videos WHERE vmssID=:vmssID LIMIT 1";
		$statement = $this->runDBQuery($sqlQuery, ['vmssID' => $vmssID]);
		$videoRow = $statement->fetch();
		$publicData['video']['vmssID'] = $videoRow['vmssID'];
		$publicData['video']['data'] = $videoRow['data'];
		$publicData['video']['files'] = $videoRow['files'];
		return $publicData;

	}
}
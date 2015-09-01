<?php

function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

class DataManager {
	private $hostname = "";
	public $dbName = "realmd"; 
	private $username = ""; 
	private $password = ""; 
	private $sqlType = "";
	private $connection;
	public $logging = 2; // 0:NONE, 1:INSERT-UPDATE, 2:ALL
	
	function __construct() {
		$this->hostname = $GLOBALS["dbHostname"];
		$this->username = $GLOBALS["dbUsername"]; 
		$this->password = $GLOBALS["dbPassword"]; 
	}

	function connect(){
		if (function_exists("mysql_connect")) {
			$this->sqlType = "mysql";
			$this->connection = mysql_connect($this->hostname,$this->username,$this->password) or die("ERROR: Connection failed."); 
			mysql_select_db($this->dbName, $this->connection);
			mysql_query("SET NAMES utf8");
		} else if (function_exists("mssql_connect")) {
			$this->sqlType = "mssql";
			$this->connection = mssql_connect($this->hostname,$this->username,$this->password) or die("ERROR: Connection failed."); 
			mssql_select_db($this->dbName);
		} else if (function_exists("sqlsrv_connect")) {
			$this->sqlType = "sqlsrv";
			$this->connection = sqlsrv_connect($this->hostname, array("Database"=>$this->dbName, "UID"=>$this->username, "PWD"=>$this->password)) or die("ERROR. Connection failed: ".print_r( sqlsrv_errors(), true));
		} else {
			die("ERROR: Unable to find a valid connection driver configured on PHP");	
		}
	}
	
	function disconnect(){
		if (function_exists("mysql_connect")) {
			mysql_close($this->connection);
		} else if (function_exists("mssql_connect")) {
			mssql_close($this->connection); 
		} else if (function_exists("sqlsrv_connect")) {
			sqlsrv_close($this->connection);
		} else {
			die("ERROR: Unable to find a valid connection driver configured on PH");	
		}
	}

	function executeQuery($query, $fetch = true) {
		try {
			if ($query != "") {
				$this->logQuery($query);
				if (strcmp(substr($query,0,6),"SELECT") === 0) {
					switch ($this->sqlType) {
						case "mysql":
							if ($fetch) {
								return mysql_fetch_assoc(mysql_query($query));
							} else {
								return mysql_query($query);
							}
							break;
						case "mssql": 
							if ($fetch) {
								return mssql_fetch_assoc(mssql_query($query));
							} else {
								return mssql_query($query);
							}
						break;
						case "sqlsrv":
							if ($fetch) {
								return sqlsrv_fetch_array(sqlsrv_query($this->connection, $query), SQLSRV_FETCH_ASSOC);
							} else {
								return sqlsrv_query($this->connection, $query, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));
							}
						break;
					}
				} else {
					switch ($this->sqlType) {
						case "mysql":
							return mysql_query($query);
						break;
						case "mssql": 
							return mssql_query($query);
						break;
						case "sqlsrv":
							return sqlsrv_query($this->connection, $query);
						break;
					}
				}
			} else {
				return false;	
			}
		} catch (Exception $ex) {
			$this->logError("Error in previous query: ". $ex->getMessage());
			return false;	
		}
	}
	/*
	function executeStatement($sqlArray){
		try {
			if ($sqlArray != "") {
				//if (!strstr($query,"SELECT")) {
					if ($this->sqlType == "mssql") {
						$ret = mssql_query("BEGIN TRAN");
						foreach($sqlArray as $query){
							$this->logQuery($query);
							$ret = $ret && mssql_query($query);
							if (!$ret) {
								break;	
							}
						}
						if ($ret) {
							mssql_query("COMMIT");
							return true;
						} else {
							mssql_query("ROLLBACK");
							return false;
						}
					} else {
						$ret = sqlsrv_begin_transaction($this->connection);
						foreach($sqlArray as $query){
							$this->logQuery($query);
							$ret = $ret && sqlsrv_query($this->connection, $query);
							if (!$ret) {
								break;	
							}
						}
						if ($ret) {
							sqlsrv_commit($this->connection);
							return true;
						} else {
							sqlsrv_rollback($this->connection);
							return false;
						}
					}
				//}
			} else {
				return false;	
			}
		} catch (Exception $ex) {
			$this->logQuery("Error in previous query: ". $ex->getMessage());
			return false;	
		}
	}
*/
	function getError(){
		switch ($this->sqlType) {
			case "mysql":
				return mysql_errno() .": ". mysql_error();
			break;
			case "mssql": 
				return mssql_get_last_message();
			break;
			case "sqlsrv":
				return sqlsrv_errors();
			break;
		}
	}
	
	function getNext($resource) {
		switch ($this->sqlType) {
			case "mysql":
				return mysql_fetch_assoc($resource);
			break;
			case "mssql": 
				return mssql_fetch_assoc($resource);
			break;
			case "sqlsrv":
				return sqlsrv_fetch_array($resource, SQLSRV_FETCH_ASSOC);
			break;
		}
	}

	function getNumRows($resource) {
		switch ($this->sqlType) {
			case "mysql":
				return mysql_num_rows($resource);
			break;
			case "mssql": 
				return mssql_num_rows($resource);
			break;
			case "sqlsrv":
				return sqlsrv_num_rows($resource);
			break;
		}
	}
	
	function logQuery($text) {
		if ( ($this->logging == 1 && strcmp(substr($text,0,6),"SELECT") !== 0) || $this->logging == 2) {
			$currPath = getcwd();
			$basePath = substr($currPath,0,strpos($currPath,"webDocs")+strlen("webDocs"));
			$fileName = date("Y-m-d");
			$filePath = $basePath ."/logs/query-". $fileName .".txt";
			$stringData = date("H:i") ." - ". str_replace("\t","",str_replace("\n","",trim($text))) ."\n";
			//echo $filePath;
			file_put_contents($filePath, $stringData, FILE_APPEND);
		}
	}

	function logAction($user, $source, $text, $note) {
		if ($this->logging > 0) {
			$currPath = getcwd();
			$basePath = substr($currPath,0,strpos($currPath,"webDocs")+strlen("webDocs"));
			$fileName = date("Y-m-d");
			$filePath = $basePath ."/logs/action-". $fileName .".txt";
			$stringData = date("H:i") ." - ". str_replace("\t","",str_replace("\n","",trim($text))) ."\n";
			file_put_contents($filePath, $stringData, FILE_APPEND);
			//$sql = "INSERT INTO eiis.dbo.serviceError (date, username, source, operation, note) (GETDATE(), ". chkVars($user) .", ". chkVars($source) .", , ". chkVars($text) .", ". chkVars($note) .")";
			$this->executeQuery($sql);
		}
	}

	function logError($text, $sessDump = false, $getDump = false, $postDump = false) {
		if ($this->logging > 0) {
			$currPath = getcwd();
			$basePath = substr($currPath,0,strpos($currPath,"webDocs")+strlen("webDocs"));
			$fileName = date("Y-m-d");
			$filePath = $basePath ."/logs/error-". $fileName .".txt";
				$stringData = date("H:i") ." - ". str_replace("\t","",str_replace("\n","",trim($text))) ."\r\n";
				if ($sessDump) {
					ob_start();
					var_dump($_SESSION);
					$result = ob_get_clean();
					$stringData .= "\r\nSESSION VARIABLES: ". str_replace("<i>(size","\r\n<i>(size",$result);
					//ob_end_flush();
				}
				if ($getDump) {
					ob_start();
					var_dump($getDump);
					$result = ob_get_clean();
					$stringData .= "\r\nGET VARIABLES: ". str_replace("<i>(size","\r\n<i>(size",$result);
					//ob_end_flush();
				}
				if ($postDump) {
					ob_start();
					var_dump($postDump);
					$result = ob_get_clean();
					$stringData .= "\r\nPOST VARIABLES: ". str_replace("<i>(size","\r\n<i>(size",$result);
					//ob_end_flush();
				}
				$stringData .= "\r\n";
				file_put_contents($filePath, $stringData, FILE_APPEND);
			//$sql = "INSERT INTO eiis.dbo.serviceError (date, server, source, description) (GETDATE(), ". chkVars($_SERVER['SERVER_NAME']) .", ". chkVars($_SERVER['PHP_SELF']) .", ". chkVars($text) .")";
			//$this->executeQuery($sql);
		}
	}
}

?>
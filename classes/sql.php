<?php

function chkString($var){
	$var = str_replace("--","",$var);
	$var = str_replace(";","",$var);
	$var = str_replace("\'","''",$var);
	if (!get_magic_quotes_gpc() && !get_magic_quotes_runtime())
	{
		$var = mysql_escape_string($var);
	}
	if (!strstr($var,"<br") && !strstr($var,"¬"))
	{
		$var = htmlentities($var, ENT_NOQUOTES, "UTF-8");
	}
	return trim($var);
}

function chkVars($var)
{
	if ($var != null || $var != "") {
		$var =  "'". chkString($var) ."'";
	} else {
		$var = "NULL";
	}
	return trim($var);
}

function chkLikeVars($var)
{
	if ($var != null || $var != "") {
		$var =  "'%". chkString($var) ."%'";
	} else {
		$var = "NULL";
	}
	return $var;	
}

function chkLLikeVars($var)
{
	if ($var != null || $var != "") {
		$var =  "'%". chkString($var) ."'";
	} else {
		$var = "NULL";
	}
	return $var;	
}

function chkRLikeVars($var)
{
	if ($var != null || $var != "") {
		$var =  "'". chkString($var) ."%'";
	} else {
		$var = "NULL";
	}
	return $var;	
}

function dataToSQL($data){
	if (strpos($data, "/") !== false) {
		list($gg,$mm,$aa) = explode("/",$data);
		$data = $aa.'-'.$mm.'-'.$gg;
	}
	return $data;
}

function dataFromSQL($data){
	if (strpos($data, "-") !== false) {
		list($aa,$mm,$gg) = explode("-",$data);
		$data = $gg.'/'.$mm.'/'.$aa;
	}
	return $data;
}

function dataIT_EN($data){
	list($gg,$mm,$aa) = explode("/",$data);
	$data = $mm.'/'.$gg.'/'.$aa;
	return $data;
}

function checkNotes($notes, $dataManager){
	// Look if there's a subtable to be displayed
	$check = preg_match("/¬(.*?)¬/",$notes, $matches);
	//print_r($check);
	//print_r($matches);
	for ($i = 1; $i <= $check;  $i++) {
		$found = $matches[round($i/2)-1];
		$id = explode(":",$matches[round($i/2)]);
		//echo '<br />Subtable '. $id[1] .' needed!!!!<br />';
		$sql = "SELECT * FROM ". $dataManager->dbName .".dbdocssubtables WHERE subTableId = ". chkVars($id[1]);
		$row = $dataManager->executeQuery($sql);
		if  ($row) {
			//echo $row["subTableContent"] .'<br />';	
			$notes = str_replace($found, $row["subTableContent"], $notes);
		} else {
			//echo '<p>Warning! Infommation not avaiable. Add into database</p>';	
			$notes = str_replace($found, "<b>**". $found ." not found**</b>", $notes);
		}
		//$notes = str_replace($found, "TABELLA!",$notes);
	}
	return $notes;	
}

function checkLink($text){
	if (isset($GLOBALS["mangosSrcPath"]) && $GLOBALS["mangosSrcPath"] != "" && file_exists($GLOBALS["mangosSrcPath"])) {
		// Look if there's a DBC link to be built
		//$checkLink = preg_match("/See (.*?).dbc/",$text, $matches);
		$checkLink = preg_match("/\([Ss]ee (.*?)\)/",$text, $matches);
		
		//print_r($checkLink);
		//print_r($matches);
		for ($i = 1; $i <= $checkLink;  $i++) {
			$found = $matches[round($i/2)-1];
			//print_r($found);
			if (strpos($found, ".dbc") !== false) {
				$dbcName = substr($found,5,strlen($found)-10);
				$dbcReplace = substr($found,5,strlen($found)-6);
				//$dbcFile = explode(":",$matches[round($i/2)]);
				$dbcStruct = file_get_contents($GLOBALS["mangosSrcPath"]."game/DBCStructure.h");
				//print_r($dbcName);
				$dbcStart = strpos(strtolower($dbcStruct), "struct ". strtolower($dbcName));
				//$checkDBC = preg_match("/struct ". $dbcName ."(.*?)/",$dbcStruct, $matchesNames);
				//print_r($checkDBC);
				//print_r($matchesNames);
				if  ($dbcStart !== false) {
					//$dbcEnd = strpos($dbcStruct, "};", $dbcStart) +2;
					//$matchContent = substr($dbcStruct, $dbcStart, $dbcEnd - $dbcStart);
					//print_r($matcheName);
					//$found = $matches[round($i/2)-1];
					//echo $row["subTableContent"] .'<br />';	
					$text = str_replace($dbcReplace, '<a href="#" onclick="showDBC(\''. $dbcName .'\', \''. $_GET["database"] .'\')">'. $dbcReplace .'</a>', $text);
				} else {
					//echo '<p>Warning! Infommation not avaiable. Add into database</p>';	
					$text = str_replace($dbcReplace, "<b>**no DBC link to ". $dbcReplace ." created**</b>", $text);
				}
			} else {
				$linkName = substr($found, 5);
				$pre = substr($found, 0, 5);
				if (strpos($linkName, ".") !== false) {
					$linkName = substr($linkName,0, strpos($linkName, "."));
				}
				$post = substr($found, strlen($linkName) + 5);
				//print_r($linkName);
				$text = str_replace($found, $pre .'<a href="#" onclick="showDetails(\'' . $linkName . '\')">' . $linkName . '</a>'. $post, $text);
			}
			//$notes = str_replace($found, "TABELLA!",$notes);
		}
	}
	return $text;
}
?>
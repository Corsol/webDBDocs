<?php
session_start();
require_once("configuration.php");
require_once("classes/sql.php");
require_once("classes/db.php");
?>
<br />
<?php
$dataManager = new DataManager();
$dataManager->dbName = $_GET["database"];
$dataManager->connect();
?>
<h3>Information from "DBCStructure.h"</h3>
<br />
<?php
if (isset($GLOBALS["mangosSrcPath"]) && $GLOBALS["mangosSrcPath"] != "" && file_exists ($GLOBALS["mangosSrcPath"])) {
	// Look if there's a DBC link to be built
	$dbcStruct = file_get_contents($GLOBALS["mangosSrcPath"]."game/DBCStructure.h");
	$dbcStart = strpos(strtolower($dbcStruct), "struct ". strtolower($_GET["dbcName"]));
	//print_r($checkDBC);
	//print_r($matchesNames);
	if  ($dbcStart !== false) {
		$dbcEnd = strpos($dbcStruct, "};", $dbcStart) + 2;
		$matchContent = substr($dbcStruct, $dbcStart, $dbcEnd - $dbcStart);
		//print_r($matchContent);
		//$found = $matches[round($i/2)-1];
		echo nl2br(str_replace(" ","&nbsp;",$matchContent));
	} else {
		//echo '<p>Warning! Infommation not avaiable. Add into database</p>';	
		echo "<b>**no DBC information got.</b>";
	}
	//$notes = str_replace($found, "TABELLA!",$notes);
}

if (isset($GLOBALS["dbcSchema"]) && $GLOBALS["dbcSchema"] != "") {
?>
	<br /><br />
	<h3>Content readed from database</h3>
	<br />
	
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblFind">
<?php	
	$sql = "SELECT * FROM ". $GLOBALS["dbcSchema"] .".dbc_". $_GET["dbcName"];
	$row = $dataManager->executeQuery($sql);
	if ($row) {
		//print_r($row);
		echo '<tr>';
		$i = 1;
		foreach ($row as $key=>$value) {
			$class = "cell";
			if ($i == 1){
				$class = "start";
			} else if ($i == count($row)) {
				$class .= " end";
			}
			echo '<th class="'. $class .' top">
				'. $key .'
			</th>';
			$i++;
		}
		echo '</tr>';
		
		
		$sql = "SELECT * FROM ". $GLOBALS["dbcSchema"] .".dbc_". $_GET["dbcName"];
		$res = $dataManager->executeQuery($sql, false);
		while ($row = $dataManager->getNext($res)) {
			//print_r($row);
			echo '<tr>';
			$i = 1;
			foreach ($row as $cell) {
				$class = "cell";
				if ($i == 1){
					$class = "start";
				} else if ($i == count($row)) {
					$class .= " end";
				}
				echo ' <td class="'. $class .'">
					'. $cell .'
				</td>';
				$i++;
			}
			echo '</tr>';
		}
	} else {
		echo "Error on reading from database: ". $dataManager->getError();
	}
?>
	</table>
<?php
}
$dataManager->disconnect();
?>

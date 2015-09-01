<?php
session_start();
require_once("configuration.php");
require_once("classes/sql.php");
require_once("classes/db.php");
$dataManager = new DataManager();
$dataManager->dbName = $_GET["database"];
$dataManager->connect();
?>

<br />
<h3 id="Top">Table <?php echo $_GET["table"]; ?></h3>
<br />

<?php
$sql = "SELECT * FROM ". $dataManager->dbName .".dbdocstable WHERE tableName = ". chkVars($_GET["table"]);
$row = $dataManager->executeQuery($sql);
if  ($row) {
	echo '<p>'. $row["tableNotes"] .'</p>';	
} else {
	echo '<p>Warning! Information not available. Add it into database</p>';	
}
?>
<br />
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblFind">
	<tr>
    	<th class="start top">
        	Column Name
        </th>
        <th class="cell top">
        	Column Description
        </th>
        <th class="cell end top">
        	Modify
        </th>
    </tr>
<?php
$i = 0;
//$sql = "SELECT * FROM ". $dataManager->dbName .".dbdocsfields WHERE tableName = ". chkVars($_GET["table"]);
$sql = "SELECT `COLUMN_NAME`, fieldComment, fieldNotes, fieldId
FROM `INFORMATION_SCHEMA`.`COLUMNS` 
LEFT JOIN ". $_GET["database"] .".dbdocsfields on `COLUMN_NAME` = fieldname COLLATE utf8_general_ci and tableName = `TABLE_NAME` COLLATE utf8_general_ci
WHERE `TABLE_SCHEMA`=". chkVars($_GET["database"]) ." 
AND `TABLE_NAME`= ". chkVars($_GET["table"]) ."
ORDER BY `ORDINAL_POSITION`";
$resource = $dataManager->executeQuery($sql, false);
if ($resource && $dataManager->getNumRows($resource) > 0) {
	while ($row = $dataManager->getNext($resource)){
		$odd = "odd";
		if ($i%2 === 0){
			$odd = "";
		}
		echo '<tr class="'. $odd .'"><td class="start"><a href="#col'. $row["fieldId"] .'">'. $row["COLUMN_NAME"] .'</a></td>
			<td class="cell">'. checkLink($row["fieldComment"]) .'</td>
			<td class="cell end"><input type="button" value="Edit row" onclick="editContent(\''. $_GET["database"] .'\',\''. $row["fieldId"] .'\',\'dbdocsfields\');"/></td>
			</tr>';
		$i++;
	}
} else {
	echo '<tr><td colspan="3" class="start end">No columns doc found for '. $_GET["table"] .' table.</td></tr>';
}
?>
</table>
<br />
<?php
//$sql = "SELECT * FROM ". $dataManager->dbName .".dbdocsfields WHERE tableName = ". chkVars($_GET["table"]);
$sql = "SELECT `COLUMN_NAME`, fieldComment, fieldNotes, fieldId
FROM `INFORMATION_SCHEMA`.`COLUMNS` 
LEFT JOIN ". $_GET["database"] .".dbdocsfields on `COLUMN_NAME` = fieldname COLLATE utf8_general_ci and tableName = `TABLE_NAME` COLLATE utf8_general_ci
WHERE `TABLE_SCHEMA`=". chkVars($_GET["database"]) ." 
AND `TABLE_NAME`= ". chkVars($_GET["table"]) ."
ORDER BY `ORDINAL_POSITION`";
$resource = $dataManager->executeQuery($sql, false);
if ($resource && $dataManager->getNumRows($resource) > 0) {
	while ($row = $dataManager->getNext($resource)){
		echo '<h4 id="col' . $row["fieldId"] . '">' . $row["COLUMN_NAME"] . '<span><a href="#Top">top</a></span></h4>';
		echo '<p>' . checkNotes(checkLink($row["fieldNotes"]), $dataManager) . '</p>';
	}
} else {
	echo '<tr><td colspan="2">No columns doc found for '. $_GET["table"] .' table.</td></tr>';
}
$dataManager->disconnect();
?>

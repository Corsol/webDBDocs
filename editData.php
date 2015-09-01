<?php
session_start();
require_once("configuration.php");
require_once("classes/sql.php");
require_once("classes/db.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Mangos Tree Docs</title>
	<link href="css/template.css" rel="stylesheet" type="text/css" />
	<link href="css/white_bg.css" rel="stylesheet" type="text/css" />
	<link href="css/table.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="jquery-ui/jquery-ui.css"/>
	<style type="text/css">
		h4 { margin: 0; padding: 0.2em; }
		h3 { margin: 0; padding: 0.2em; text-align: center; }
		h2 { margin: 0px; padding: 0px; text-align: center; }
		
		.tblFind { width: auto; }
	</style>
	<link href="css/green.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="jquery-ui/external/jquery/jquery.js"></script>
	<script type="text/javascript" src="jquery-ui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(function() {
		});

	</script>
 </head>

<body>
<br />
<form action="" method="get">
<?php
$dataManager = new DataManager();
$dataManager->dbName = $_GET["database"];
$dataManager->connect();

$editType = $_GET["type"];
$editRowId = $_GET["id"];

if (isset($editRowId)){
	$sql = "";
	switch($editType) {
		case "dbdocstable":
		break;
		case "dbdocsubstable":
		break;
		case "dbdocsfields":
			if (!isset($_GET["applyModify"])){
				$sql = "SELECT * FROM ". $dataManager->dbName .".dbdocsfields WHERE fieldId = ". chkVars($editRowId);
				$row = $dataManager->executeQuery($sql);
				if  ($row) {
?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblFind">
        <tr>
            <th class="start top">
                Field ID
            </th>
            <td class="cell end top">
            	<?php echo $editRowId; ?>
            </td>
        </tr>
        <tr>
            <th class="start">
                Table Name
            </th>
            <td class="cell end ">
            	<?php echo $row["tableName"]; ?>
            </td>
        </tr>
        <tr>
            <th class="start">
                Field Name
            </th>
            <td class="cell end">
            	<?php echo $row["fieldName"]; ?>
            </td>
        </tr>
        <tr>
            <th class="start">
                Field Comment
            </th>
            <td class="cell end">
            	<textarea name="txtFieldComment" cols="60" rows="8"><?php echo $row["fieldComment"]; ?></textarea>
            </td>
        </tr>
        <tr>
            <th class="start">
                Field Notes
            </th>
            <td class="cell end">
            	<textarea name="txtFieldNotes" cols="60" rows="10"><?php echo $row["fieldNotes"]; ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="start end" colspan="2" align="center">
            	<input type="hidden" name="database" value="<?php echo $_GET["database"]; ?>" />
            	<input type="hidden" name="type" value="<?php echo $editType; ?>" />
            	<input type="hidden" name="id" value="<?php echo $editRowId; ?>" />
            	<input type="hidden" name="applyModify" value="true" />
                <input type="submit" value="Modify" />
            </td>
        </tr>
    </table>


<?php
				} else {
					echo "Unable to identify the object to edit";
				}
			} else {
				//do the modify	
				$sql = "UPDATE ". $dataManager->dbName .".dbdocsfields 
						SET fieldComment = ". chkVars($_GET["txtFieldComment"]) .", fieldNotes = ". chkVars($_GET["txtFieldNotes"]) ." 
						WHERE fieldId = ". chkVars($editRowId);
				$editRes = $dataManager->executeQuery($sql);
				if  ($editRes) {
					echo "Modify applied with success! This is the query to copy-paste into SQL updates files: <br/> 
						<b>UPDATE dbdocsfields 
						SET fieldComment = ". htmlentities(chkVars($_GET["txtFieldComment"])) .", fieldNotes = ". htmlentities(chkVars($_GET["txtFieldNotes"])) ." 
						WHERE fieldId = ". chkVars($editRowId) ."</b>";
				} else {
					echo "There was errors on update. Operation aborted.";
				}
			}
		break;
		default:
			echo "Subject to modify is missing.";
		break;	
	}

?>
</form>
<?php
} else {
	echo "Row identifier missing."	;
}

$dataManager->disconnect();
?>

</body>
</html>
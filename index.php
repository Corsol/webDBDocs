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
	<link rel="stylesheet" type="text/css" href="jquery-ui/jquery-ui.css"/>
	<style type="text/css">
		h4 { margin: 0; padding: 0.2em; text-align: center; }
		h3 { margin: 0; padding: 0.2em; text-align: center; }
		h2 { margin: 0px; padding: 0px; text-align: center; }
	</style>
	<link href="css/green.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="jquery-ui/external/jquery/jquery.js"></script>
	<script type="text/javascript" src="jquery-ui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(function() {
			$("#db-tabs").tabs({
				ajaxOptions: {
					error: function(xhr, status, index, anchor) {
						$(anchor.hash).html("Couldn't load this tab. We'll try to fix this as soon as possible. If this wouldn't be a demo.");
					}
				},
				cache: true
			});
		});

		function ifrmResize(ifrm, height) {
			//find the height of the internal page
			//var doc = document.getElementById("ifrmAppStatus").contentWindow.document.scrollHeight;
			document.getElementById(ifrm).height = 0;
			var the_height = 0;
			if (height == undefined) {
				the_height = document.getElementById(ifrm).contentWindow.document.body.scrollHeight;
			} else {
				the_height = height;
			}
			//var the_width =  document.getElementById(id).contentWindow.document.body.scrollWidth;
			//change the height of the iframe
			//Math.max($(document).height(),$(window).height())
			//alert(the_height);
			document.getElementById(ifrm).height = the_height;
			//document.getElementById(id).width = the_width;
			//unLoad = true;
			//$("#waiting").dialog('close');
		}

	</script>
</head>

<body>
<?php
include_once("structure/header.php");

$dataManager = new DataManager();
?>
        <div id="db-tabs">
            <ul>
<?php
foreach ($databases as $db) {
	echo '<li><a href="#' . $db . '">Database "' . $db . '"</a></li>';
}
?>
            </ul>
<?php
foreach ($databases as $db) {
	//$dataManager->dbName = $db;
	//$dataManager->connect();
	echo '<div id="' . $db . '"><iframe id="ifrm' . $db . '" align="middle" width="100%" height="550" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" class="iframe" src="dbStructure.php?database=' . $db . '">Caricamento dei dati in corso.</iframe></div>';
}
?>
        </div>


<?php	
include_once("structure/footer.php");
?>
</body>
</html>
<?php

/*
SELECT `COLUMN_NAME`, fieldComment, fieldNotes 
FROM `INFORMATION_SCHEMA`.`COLUMNS` 
LEFT JOIN dbdocsfields on `COLUMN_NAME` = fieldname COLLATE utf8_general_ci and tableName = `TABLE_NAME` COLLATE utf8_general_ci
WHERE `TABLE_SCHEMA`='mangos' 
AND `TABLE_NAME`='transports';
*/

?>
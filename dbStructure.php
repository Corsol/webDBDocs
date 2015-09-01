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
		h4 { font-size: 16px;}
		h3 { margin: 0; padding: 0.2em; text-align: center; }
		h2 { margin: 0px; padding: 0px; text-align: center; }
		
		.tblFind { width: auto; }
		
		h4 span { float: right; font-size: 12px;}
		h4 span a {}
		/*pre {word-wrap:break-word;}*/
	</style>
	<link href="css/green.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="jquery-ui/external/jquery/jquery.js"></script>
	<script type="text/javascript" src="jquery-ui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(function() {
			$("#divEditData").dialog({
			autoOpen: false,
			modal: true,
			height: 'auto',
			width: 'auto',
			position: {my: "center", at: "top", of: window},
			minWidth: 500,
			minHeight: 400,
			maxWidth: 900,
			maxHeight: 700,
			open: function(event, ui) {
				},
			close: function(event, ui) {
				}
			});
		    $("#divList").tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
		    $("#divList li").removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
		});

		function showDetails(table){
			$("#divDetails").load('tableDetails.php?database=<?php echo $_GET["database"]; ?>&table='+table);
		}
		
		function editContent(db, rowId, table){
			$("#divContent").html("");
			url = "editData.php?database="+db+"&id="+rowId+"&type="+table;
			$("#ifrmEdit").attr("width","600");
			$("#ifrmEdit").attr("height","500");
			$("#ifrmEdit").attr("src",url);
			$("#divEditData").dialog('open');
		}
		
		function showDBC(dbcName, db){
			$("#divContent").html("");
			url = "dbcContent.php?database="+db+"&dbcName="+dbcName;
			$("#divContent").load(url);
			$("#divEditData").dialog('open');
			$("#ifrmEdit").attr("width","0");
			$("#ifrmEdit").attr("height","0");
		}

		function resizeTop() {
			window.top.ifrmResize('ifrm<?php echo $_GET["database"];?>', document.body.scrollHeight);
		//alert(document.body.scrollHeight);
		}		
	</script>
  <style>
  .ui-tabs-vertical { width: auto; }
  .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 98%; }
  .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
  .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; }
  .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}
  .ui-tabs-anchor { width: 90% }
  </style>
 </head>

<body onload="window.top.ifrmResize('ifrm<?php echo $_GET["database"];?>', document.body.scrollHeight);">
<div style="display:table; width:100%;">
	<div style="display:table-cell; padding: 5px; vertical-align: top;  width:25%">
        <div class="ui-dialog ui-widget ui-widget-content ui-corner-all" style="position: relative; margin-bottom: 10px;">
            <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                <span class="ui-dialog-title">
                	Documented tables
                </span>
            </div>
            <div class="ui-dialog-content ui-widget-content" style="width: auto; height: auto; text-align: left; height: 700px; overflow:auto" id="divTables">
            	<div id="divList">
	            <ul>
<?php
	$dataManager = new DataManager();
	$dataManager->dbName = $_GET["database"];
	$dataManager->connect();
	$sql = "SELECT * FROM ". $dataManager->dbName .".dbdocstable";
	$resource = $dataManager->executeQuery($sql, false);
	if ($resource) {
		while ($row = $dataManager->getNext($resource)){
			echo '<li><a href="#divTab" onclick="showDetails(\'' . $row["tableName"] . '\')">' . $row["tableName"] . '</a></li>';
		}
	} else {
		echo "No dbdocstable found for docs!";	
	}
	$dataManager->disconnect();
?>
	            </ul>
                <div id="divTab" style="display: none; width: 0px; height: 0px;">
                </div>
                </div>
            </div>
        </div>
    </div>
	<div style="display:table-cell; padding: 5px; vertical-align: top;  width:75%">
        <div class="ui-dialog ui-widget ui-widget-content ui-corner-all" style="position: relative;">
            <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                <span class="ui-dialog-title">
                	Tables details
                </span>
            </div>
            <div class="ui-dialog-content ui-widget-content" style="width: auto; height: auto; text-align: left; height: 700px; overflow:auto;" id="divDetails">
            </div>
        </div>
	</div>
</div>
<div id="divEditData" title="Edit contents">
	<div id="divContent">
    </div>
    <iframe id="ifrmEdit" align="middle" width="600" height="450" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto" class="iframe" src="">Caricamento dei dati in corso.</iframe>
</div>
</body>
</html>
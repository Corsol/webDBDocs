<?php
@session_start();
if (!isset($_SESSION["autenticato"]) || $_SESSION["autenticato"] == "") {
?>
<script type="text/javascript">
	document.location = 'index.php?logout=true';
</script>
<?php
}
?>
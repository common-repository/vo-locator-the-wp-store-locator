<?php
/*if (empty($_POST)) {sl_move_upload_directories();}*/
if (empty($_GET['pg'])) {
	include(VOSL_PAGES_PATH."/manage-locations.php");
} else {
	$the_page = VOSL_PAGES_PATH."/".$_GET['pg'].".php";
	if (file_exists($the_page)) {
		include($the_page);
	}
}
?>
<?php
	require("_header.php");
	
    echo '<h2>Infos</h2>';

    echo PageContent(1,CheckEditPermission());

	include("_footer.php");
?>
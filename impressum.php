<?php
	require("_header.php");

    NavBar("Home","Impressum");   

    echo '<h2>Impressum</h2>';

    echo PageContent(1,CheckEditPermission());

	include("_footer.php");
?>
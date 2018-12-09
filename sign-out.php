<?php
	require("_header.php");

    session_destroy();
    Page::Redirect("/");
	
	include("_footer.php");
?>
<?php
	require("_header.php");

    NavBar("Home","Abmelden");

    session_destroy();
    Page::Redirect("/");
	
	include("_footer.php");
?>
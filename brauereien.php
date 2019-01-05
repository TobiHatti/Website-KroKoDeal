<?php
	require("_header.php");

    NavBar("Home","Brauereien"); 

    echo '<h2>Brauereien</h2><br>';

    echo '<center>';

    $rows = MySQL::Cluster("SELECT * FROM countries INNER JOIN breweries ON breweries.countryID = countries.id WHERE countries.id = breweries.countryID GROUP BY countries.id");
    foreach($rows AS $row) echo CountryButton($row['countryShort'],false,false,true,true);

    echo '</center>';

	include("_footer.php");
?>
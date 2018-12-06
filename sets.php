<?php
	require("_header.php");

    if(!isset($_GET['country']))
    {
        echo '<h2>Sets</h2>';

        echo '<center>';
        $buttonArray = MySQL::Cluster("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE isSet = '1' GROUP BY countries.countryShort");
        foreach($buttonArray AS $button) echo CountryButton($button['countryShort'],false,true,true);
        echo '</center>';
    }
    else
    {
        $country = $_GET['country'];
        $countryData = MySQL::Row("SELECT * FROM countries WHERE countryShort = ?",'s',$country);

        echo '<h2>Sets aus '.$countryData['countryDE'].'</h2>';

        $setDataArray = MySQL::Cluster("SELECT * FROM sets INNER JOIN bottlecaps ON sets.id = bottlecaps.setID INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE countries.countryShort = ? GROUP BY bottlecaps.setID ORDER BY sets.setName ASC",'s',$country);
        echo '<center>';
        foreach($setDataArray AS $setTile) echo SetTile($setTile['setID']);
        echo '</center>';
    }

	include("_footer.php");
?>
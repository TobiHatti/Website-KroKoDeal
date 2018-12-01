<?php
	require("_header.php");

    if(isset($_GET['kontinent']))
    {
        $continent = $_GET['kontinent'];

        echo '<h2>'.MySQL::Scalar("SELECT continentDE FROM continents WHERE continentShort = ?",'s',$continent).'</h2><br>';

        echo '<center>';

        $rows = MySQL::Cluster("SELECT * FROM countries INNER JOIN continents ON countries.continentID = continents.id WHERE continents.continentShort = ?",'s',$continent);
        foreach($rows AS $row) echo CountryButton($row['countryShort'],true,true);

        echo '<br><br><br>';

        $rows = MySQL::Cluster("SELECT * FROM continents");
        foreach($rows AS $row) echo ContinentButton($row['continentShort'],true,true);

        echo '</center>';
    }
    else if(isset($_GET['region']))
    {
        $country = $_GET['region'];

        echo '<h2>'.MySQL::Scalar("SELECT countryDE FROM countries WHERE countryShort = ?",'s',$country).'</h2><br>';

        echo '<center>';
        $rows = MySQL::Cluster("SELECT * FROM regions INNER JOIN countries ON regions.countryID = countries.id WHERE countries.countryShort = ?",'s',$country);
        foreach($rows AS $row) echo RegionButton($row['regionShort'],true,true);
        echo '</center>';
    }
    else
    {
        echo '<h2>L&auml;nder</h2><br>';

        echo '<center>';

        $rows = MySQL::Cluster("SELECT * FROM countries");
        foreach($rows AS $row) echo CountryButton($row['countryShort'],true,true);

        echo '<br><br><br>';

        $rows = MySQL::Cluster("SELECT * FROM continents");
        foreach($rows AS $row) echo ContinentButton($row['continentShort'],true,true);

        echo '</center>';
    }


	
	include("_footer.php");
?>
<?php
	require("_header.php");

    if(isset($_GET['continent']))
    {
        NavBar("Home","Continent:".$_GET['continent']);

        $continent = $_GET['continent'];

        echo '<h2>'.MySQL::Scalar("SELECT continentDE FROM continents WHERE continentShort = ?",'s',$continent).'</h2><br>';

        echo '<center>';

        $rows = MySQL::Cluster("SELECT * FROM countries INNER JOIN continents ON countries.continentID = continents.id WHERE continents.continentShort = ?",'s',$continent);
        foreach($rows AS $row) echo CountryButton($row['countryShort'],true,false,true);

        echo '<br><br><br>';

        $rows = MySQL::Cluster("SELECT * FROM continents");
        foreach($rows AS $row) echo ContinentButton($row['continentShort'],true,true);

        echo '</center>';
    }
    else if(isset($_GET['region']))
    {
        NavBar("Home","Laender","CountrySub:".$_GET['region']);

        $country = $_GET['region'];
        $countryData = MySQL::Row("SELECT * FROM countries WHERE countryShort = ?",'s',$country);

        echo '<h2>'.$countryData['countryDE'].'</h2><br>';

        echo '<center>';
        $rows = MySQL::Cluster("SELECT * FROM regions INNER JOIN countries ON regions.countryID = countries.id WHERE countries.countryShort = ?",'s',$country);
        foreach($rows AS $row) echo RegionButton($row['regionShort'],true,true);

        echo '<br><br><br>';
        echo SetsAndA2ZButtons($countryData['countryShort'],true,true);

        echo '</center>';
    }
    else
    {
        NavBar("Home","Laender");

        echo '<h2>L&auml;nder</h2><br>';

        echo '<center>';

        $rows = MySQL::Cluster("SELECT * FROM countries");
        foreach($rows AS $row) echo CountryButton($row['countryShort'],true,false,true);

        echo '<br><br><br>';

        $rows = MySQL::Cluster("SELECT * FROM continents");
        foreach($rows AS $row) echo ContinentButton($row['continentShort'],true,true);

        echo '</center>';
    }


	
	include("_footer.php");
?>
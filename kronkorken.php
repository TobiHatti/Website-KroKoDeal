<?php
	require("_header.php");


//========================================================================================
//========================================================================================
//  START COUNTRY AND REGION SELECTION
//========================================================================================
//========================================================================================
    if(!isset($_GET['collection']))
    {
        if(isset($_GET['region']))
        {
            $country = $_GET['country'];
            $region = $_GET['region'];
            $countryData = MySQL::Row("SELECT * FROM countries WHERE countryShort = ?",'s',$country);
            $regionData = MySQL::Row("SELECT * FROM regions WHERE regionShort = ?",'s',$region);
            $pager = new Pager(20);
            $pagerOffset = $pager->GetOffset();
            $pagerSize = $pager->GetPagerSize();

            echo '<h2>Kronkorken aus '.$countryData['countryDE'].'</h2>';

            echo '<center>';

            echo '<a href="/kronkorken/AUT/alle">Alle Anzeigen</a><br><br>';

            $alphaSortData = MySQL::Cluster("SELECT DISTINCT LEFT(breweries.breweryName , 1) AS letter FROM breweries INNER JOIN countries ON breweries.countryID = countries.id INNER JOIN regions ON breweries.regionID = regions.id WHERE countries.countryShort = ? AND regions.regionShort = ? ORDER BY breweries.breweryName ASC",'ss',$country,$region);
            echo 'Nach Brauerei-Anfangsbuchstaben sortieren<br><div class="letterSelection">';
            foreach($alphaSortData AS $letter) echo '<a href="/kronkorken/sammlung/AUT/sortiert/'.$letter['letter'].'"><span>'.$letter['letter'].'</span></a>';
            echo '</div><br><br>';

            echo 'Nach Brauerei sortieren';
            echo $pager->SQLAuto("SELECT id FROM breweries WHERE countryID = ? AND regionID = ? ORDER BY breweryName ASC",'ii',$countryData['id'],$regionData['id']);

            echo '<table class="breweryListTable">';
            $breweryList = MySQL::Cluster("SELECT id FROM breweries WHERE countryID = ? AND regionID = ? ORDER BY breweryName ASC LIMIT ?,?",'iiii',$countryData['id'],$regionData['id'],$pagerOffset,$pagerSize);
            foreach($breweryList AS $brewery) echo BreweryListTile($brewery['id'],true);
            echo '</table>';


            echo $pager->SQLAuto("SELECT id FROM breweries WHERE countryID = ? AND regionID = ? ORDER BY breweryName ASC",'ii',$countryData['id'],$regionData['id']);                             
            echo '</center>';
        }
        else if(isset($_GET['country']))
        {
            $country = $_GET['country'];
            $countryData = MySQL::Row("SELECT * FROM countries WHERE countryShort = ?",'s',$country);
            $pager = new Pager(20);
            $pagerOffset = $pager->GetOffset();
            $pagerSize = $pager->GetPagerSize();

            $countryHasRegions = MySQL::Exist("SELECT breweries.id FROM breweries INNER JOIN countries ON countries.id = breweries.countryID INNER JOIN regions ON regions.id = breweries.regionID WHERE regions.countryID = ?",'i',$countryData['id']);

            echo '<h2>Kronkorken aus '.$countryData['countryDE'].'</h2>';

            echo '<center>';

            echo '<a href="/kronkorken/AUT/alle">Alle Anzeigen</a><br><br>';

            $alphaSortData = MySQL::Cluster("SELECT DISTINCT LEFT(breweries.breweryName , 1) AS letter FROM breweries INNER JOIN countries ON breweries.countryID = countries.id WHERE countries.countryShort = ? ORDER BY breweries.breweryName ASC",'s',$country);
            echo 'Nach Brauerei-Anfangsbuchstaben sortieren<br><div class="letterSelection">';
            foreach($alphaSortData AS $letter) echo '<a href="/kronkorken/sammlung/AUT/sortiert/'.$letter['letter'].'"><span>'.$letter['letter'].'</span></a>';
            echo '</div><br><br>';

            echo 'Nach Brauerei sortieren';
            echo $pager->SQLAuto("SELECT id FROM breweries WHERE countryID = ? ORDER BY breweryName ASC",'i',$countryData['id']);

            echo '<table class="breweryListTable">';
            $breweryList = MySQL::Cluster("SELECT id FROM breweries WHERE countryID = ? ORDER BY breweryName ASC LIMIT ?,?",'iii',$countryData['id'],$pagerOffset,$pagerSize);
            foreach($breweryList AS $brewery) echo BreweryListTile($brewery['id'],$countryHasRegions);
            echo '</table>';


            echo $pager->SQLAuto("SELECT id FROM breweries WHERE countryID = ? ORDER BY breweryName ASC",'i',$countryData['id']);
            echo '</center>';
        }
    }

//========================================================================================
//========================================================================================
//  START OF BOTTLECAP DISPLAYS
//========================================================================================
//========================================================================================

    if(isset($_GET['collection']))
    {
        $country = $_GET['country'];
        $region = $_GET['region'];
        $brewery = $_GET['brewery'];
    }


	include("_footer.php");
?>
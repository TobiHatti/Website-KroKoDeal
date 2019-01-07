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
            NavBar("Home","Laender","CountrySub:".$_GET['country'],"Region:".$_GET['region']);

            $country = $_GET['country'];
            $region = $_GET['region'];
            $countryData = MySQL::Row("SELECT * FROM countries WHERE countryShort = ?",'s',$country);
            $regionData = MySQL::Row("SELECT * FROM regions WHERE regionShort = ?",'s',$region);
            $pager = new Pager(20);
            $pagerOffset = $pager->GetOffset();
            $pagerSize = $pager->GetPagerSize();

            echo '<h2>Kronkorken aus '.$countryData['countryDE'].'</h2>';

            echo '<center>';

            echo '<a href="/kronkorken/'.$country.'/'.$region.'/alle">Alle Anzeigen</a><br><br>';

            $alphaSortData = MySQL::Cluster("SELECT DISTINCT LEFT(breweries.breweryName , 1) AS letter FROM breweries INNER JOIN countries ON breweries.countryID = countries.id INNER JOIN regions ON breweries.regionID = regions.id WHERE countries.countryShort = ? AND regions.regionShort = ? ORDER BY breweries.breweryName ASC",'ss',$country,$region);
            echo 'Nach Brauerei-Anfangsbuchstaben sortieren<br><div class="letterSelection">';
            foreach($alphaSortData AS $letter) echo '<a href="/kronkorken/sammlung/'.$country.'/'.$region.'/sortiert/'.$letter['letter'].'"><span>'.$letter['letter'].'</span></a>';
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
            NavBar("Home","Laender","Country:".$_GET['country']);

            $country = $_GET['country'];
            $countryData = MySQL::Row("SELECT * FROM countries WHERE countryShort = ?",'s',$country);
            $pager = new Pager(20);
            $pagerOffset = $pager->GetOffset();
            $pagerSize = $pager->GetPagerSize();

            $countryHasRegions = MySQL::Exist("SELECT breweries.id FROM breweries INNER JOIN countries ON countries.id = breweries.countryID INNER JOIN regions ON regions.id = breweries.regionID WHERE regions.countryID = ?",'i',$countryData['id']);

            echo '<h2>Kronkorken aus '.$countryData['countryDE'].'</h2>';

            echo '<center>';

            echo '<a href="/kronkorken/'.$country.'/alle">Alle Anzeigen</a><br><br>';

            $alphaSortData = MySQL::Cluster("SELECT DISTINCT LEFT(breweries.breweryName , 1) AS letter FROM breweries INNER JOIN countries ON breweries.countryID = countries.id WHERE countries.countryShort = ? ORDER BY breweries.breweryName ASC",'s',$country);
            echo 'Nach Brauerei-Anfangsbuchstaben sortieren<br><div class="letterSelection">';
            foreach($alphaSortData AS $letter) echo '<a href="/kronkorken/sammlung/'.$country.'/sortiert/'.$letter['letter'].'"><span>'.$letter['letter'].'</span></a>';
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
        $pager = new Pager(20);
        $pagerOffset = $pager->GetOffset();
        $pagerSize = $pager->GetPagerSize();

        if(isset($_GET['sortbyletter']))
        {
            if(isset($_GET['region']))
            {
                NavBar("Home","Laender","CountrySub:".$_GET['country'],"Region:".$_GET['region'],"Letter:".$_GET['letter']);

                $country = $_GET['country'];
                $region = $_GET['region'];
                $letter = $_GET['letter'].'%';

                if(MySQL::Exist("SELECT regions.id FROM regions INNER JOIN countries ON regions.countryID = countries.id WHERE countries.countryShort = ?",'s',$country)) $countryHasRegions = true;
                else $countryHasRegions = false;

                $sqlStatement = "SELECT *,
                bottlecaps.id AS bottlecapID,
                capColor.colorShort AS bottlecapCapColorShort,
                capColor.colorDE AS bottlecapCapColorName,
                baseColor.hex AS bottlecapBaseColorValue,
                baseColor.colorDE AS bottlecapBaseColorName,
                textColor.hex AS bottlecapTextColorValue,
                textColor.colorDE AS bottlecapTextColorName
                FROM bottlecaps
                INNER JOIN breweries ON bottlecaps.breweryID = breweries.id
                INNER JOIN countries ON breweries.countryID = countries.id
                ".($countryHasRegions ? "INNER JOIN regions ON breweries.regionID = regions.id" : "")."
                INNER JOIN flavors ON bottlecaps.flavorID = flavors.id
                INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id
                INNER JOIN colors AS capColor ON bottlecaps.capColorID = capColor.id
                INNER JOIN colors AS baseColor ON bottlecaps.baseColorID = baseColor.id
                INNER JOIN colors AS textColor ON bottlecaps.textColorID = textColor.id
                WHERE countries.countryShort = ?
                AND regions.regionShort = ?
                AND breweries.breweryName LIKE ?
                ORDER BY breweries.breweryName, bottlecaps.capNumber ASC
                LIMIT $pagerOffset,$pagerSize";



                $capDataArray = MySQL::Cluster($sqlStatement,'@s',$country,$region,$letter);
                $sqlPager = $pager->SQLAuto(str_replace(" LIMIT $pagerOffset,$pagerSize","",$sqlStatement),'@s',$country,$region,$letter);
                $tableHeader = 'Kronkorken aus '.$capDataArray[0]['countryDE'].' - '.$capDataArray[0]['regionDE'].' beginnend mit "'.$_GET['letter'].'"';


                $alphaSortData = MySQL::Cluster("SELECT DISTINCT LEFT(breweries.breweryName , 1) AS letter FROM breweries INNER JOIN countries ON breweries.countryID = countries.id INNER JOIN regions ON breweries.regionID = regions.id WHERE countries.countryShort = ? AND regions.regionShort = ? ORDER BY breweries.breweryName ASC",'ss',$country,$region);
                $alphaSort = '<div class="letterSelection">';
                foreach($alphaSortData AS $letter) $alphaSort .= '<a href="/kronkorken/sammlung/'.$country.'/'.$region.'/sortiert/'.$letter['letter'].'"><span '.((isset($_GET['letter']) AND $_GET['letter'] == $letter['letter']) ? 'id="selected"' : '' ).'>'.$letter['letter'].'</span></a>';
                $alphaSort .= '</div>';
            }
            else
            {
                NavBar("Home","Laender","Country:".$_GET['country'],"Letter:".$_GET['letter']);

                $country = $_GET['country'];
                $letter = $_GET['letter'].'%';

                if(MySQL::Exist("SELECT regions.id FROM regions INNER JOIN countries ON regions.countryID = countries.id WHERE countries.countryShort = ?",'s',$country)) $countryHasRegions = true;
                else $countryHasRegions = false;

                $sqlStatement = "SELECT *,
                bottlecaps.id AS bottlecapID,
                capColor.colorShort AS bottlecapCapColorShort,
                capColor.colorDE AS bottlecapCapColorName,
                baseColor.hex AS bottlecapBaseColorValue,
                baseColor.colorDE AS bottlecapBaseColorName,
                textColor.hex AS bottlecapTextColorValue,
                textColor.colorDE AS bottlecapTextColorName
                FROM bottlecaps
                INNER JOIN breweries ON bottlecaps.breweryID = breweries.id
                INNER JOIN countries ON breweries.countryID = countries.id
                ".($countryHasRegions ? "INNER JOIN regions ON breweries.regionID = regions.id" : "")."
                INNER JOIN flavors ON bottlecaps.flavorID = flavors.id
                INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id
                INNER JOIN colors AS capColor ON bottlecaps.capColorID = capColor.id
                INNER JOIN colors AS baseColor ON bottlecaps.baseColorID = baseColor.id
                INNER JOIN colors AS textColor ON bottlecaps.textColorID = textColor.id
                WHERE countries.countryShort = ?
                AND breweries.breweryName LIKE ?
                ORDER BY breweries.breweryName, bottlecaps.capNumber ASC
                LIMIT $pagerOffset,$pagerSize";

                $capDataArray = MySQL::Cluster($sqlStatement,'@s',$country,$letter);
                $sqlPager = $pager->SQLAuto(str_replace(" LIMIT $pagerOffset,$pagerSize","",$sqlStatement),'@s',$country,$letter);
                $tableHeader = 'Kronkorken aus '.$capDataArray[0]['countryDE'].' beginnend mit "'.$_GET['letter'].'"';

                $alphaSortData = MySQL::Cluster("SELECT DISTINCT LEFT(breweries.breweryName , 1) AS letter FROM breweries INNER JOIN countries ON breweries.countryID = countries.id WHERE countries.countryShort = ? ORDER BY breweries.breweryName ASC",'s',$country);
                $alphaSort = '<div class="letterSelection">';
                foreach($alphaSortData AS $letter) $alphaSort .= '<a href="/kronkorken/sammlung/'.$country.'/sortiert/'.$letter['letter'].'"><span '.((isset($_GET['letter']) AND $_GET['letter'] == $letter['letter']) ? 'id="selected"' : '' ).'>'.$letter['letter'].'</span></a>';
                $alphaSort .= '</div>';
            }
        }
        if(isset($_GET['sortbybrewery']))
        {
            NavBar("Home","Laender","Country:".$_GET['country'],"Brewery:".$_GET['brewery']);

            $country = $_GET['country'];
            $brewery = $_GET['brewery'];

            if(MySQL::Exist("SELECT regions.id FROM regions INNER JOIN countries ON regions.countryID = countries.id WHERE countries.countryShort = ?",'s',$country)) $countryHasRegions = true;
            else $countryHasRegions = false;

            $sqlStatement = "SELECT *,
            bottlecaps.id AS bottlecapID,
            capColor.colorShort AS bottlecapCapColorShort,
            capColor.colorDE AS bottlecapCapColorName,
            baseColor.hex AS bottlecapBaseColorValue,
            baseColor.colorDE AS bottlecapBaseColorName,
            textColor.hex AS bottlecapTextColorValue,
            textColor.colorDE AS bottlecapTextColorName
            FROM bottlecaps
            INNER JOIN breweries ON bottlecaps.breweryID = breweries.id
            INNER JOIN countries ON breweries.countryID = countries.id
            ".($countryHasRegions ? "INNER JOIN regions ON breweries.regionID = regions.id" : "")."
            INNER JOIN flavors ON bottlecaps.flavorID = flavors.id
            INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id
            INNER JOIN colors AS capColor ON bottlecaps.capColorID = capColor.id
            INNER JOIN colors AS baseColor ON bottlecaps.baseColorID = baseColor.id
            INNER JOIN colors AS textColor ON bottlecaps.textColorID = textColor.id
            WHERE countries.countryShort = ?
            AND breweries.breweryFilepath = ?
            ORDER BY breweries.breweryName, bottlecaps.capNumber ASC
            LIMIT $pagerOffset,$pagerSize";

            $capDataArray = MySQL::Cluster($sqlStatement,'@s',$country,$brewery);
            $sqlPager = $pager->SQLAuto(str_replace(" LIMIT $pagerOffset,$pagerSize","",$sqlStatement),'@s',$country,$brewery);
            $tableHeader = 'Kronkorken aus '.$capDataArray[0]['countryDE'].' der Brauerei "'.$capDataArray[0]['breweryName'].'"';
        }
        if(isset($_GET['all']))
        {
            NavBar("Home","Sammlung");

            if(isset($_GET['letter'])) $letter = $_GET['letter'].'%';
            else $letter="";

            $sqlStatement = "SELECT *,
            bottlecaps.id AS bottlecapID,
            capColor.colorShort AS bottlecapCapColorShort,
            capColor.colorDE AS bottlecapCapColorName,
            baseColor.hex AS bottlecapBaseColorValue,
            baseColor.colorDE AS bottlecapBaseColorName,
            textColor.hex AS bottlecapTextColorValue,
            textColor.colorDE AS bottlecapTextColorName
            FROM bottlecaps
            INNER JOIN breweries ON bottlecaps.breweryID = breweries.id
            INNER JOIN countries ON breweries.countryID = countries.id
            INNER JOIN flavors ON bottlecaps.flavorID = flavors.id
            INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id
            INNER JOIN colors AS capColor ON bottlecaps.capColorID = capColor.id
            INNER JOIN colors AS baseColor ON bottlecaps.baseColorID = baseColor.id
            INNER JOIN colors AS textColor ON bottlecaps.textColorID = textColor.id
            ".(isset($_GET['letter']) ? "WHERE breweries.breweryName LIKE ? AND isOwned = '1'" : "WHERE isOwned = '1'")."
            ORDER BY breweries.breweryName, bottlecaps.capNumber ASC
            LIMIT $pagerOffset,$pagerSize";

            $countryHasRegions = false;

            if(isset($_GET['letter'])) $capDataArray = MySQL::Cluster($sqlStatement,'s',$letter);
            else $capDataArray = MySQL::Cluster($sqlStatement);

            if(isset($_GET['letter'])) $sqlPager = $pager->SQLAuto(str_replace(" LIMIT $pagerOffset,$pagerSize","",$sqlStatement),'s',$letter);
            else $sqlPager = $pager->SQLAuto(str_replace(" LIMIT $pagerOffset,$pagerSize","",$sqlStatement));

            $tableHeader = 'Alle Kronkorken';

            $alphaSortData = MySQL::Cluster("SELECT DISTINCT LEFT(breweries.breweryName , 1) AS letter FROM breweries INNER JOIN countries ON breweries.countryID = countries.id ORDER BY breweries.breweryName ASC");
            $alphaSort = '<div class="letterSelection">';
            foreach($alphaSortData AS $letter) $alphaSort .= '<a href="/kronkorken/alle/'.$letter['letter'].'"><span '.((isset($_GET['letter']) AND $_GET['letter'] == $letter['letter']) ? 'id="selected"' : '' ).'>'.$letter['letter'].'</span></a>';
            $alphaSort .= '</div>';
        }
        if(isset($_GET['noImage']))
        {
            NavBar("Home","Sammlung","Kronkorken ohne Bild");

            $sqlStatement = "SELECT *,
            bottlecaps.id AS bottlecapID,
            capColor.colorShort AS bottlecapCapColorShort,
            capColor.colorDE AS bottlecapCapColorName,
            baseColor.hex AS bottlecapBaseColorValue,
            baseColor.colorDE AS bottlecapBaseColorName,
            textColor.hex AS bottlecapTextColorValue,
            textColor.colorDE AS bottlecapTextColorName
            FROM bottlecaps
            INNER JOIN breweries ON bottlecaps.breweryID = breweries.id
            INNER JOIN countries ON breweries.countryID = countries.id
            INNER JOIN flavors ON bottlecaps.flavorID = flavors.id
            INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id
            INNER JOIN colors AS capColor ON bottlecaps.capColorID = capColor.id
            INNER JOIN colors AS baseColor ON bottlecaps.baseColorID = baseColor.id
            INNER JOIN colors AS textColor ON bottlecaps.textColorID = textColor.id
            ORDER BY breweries.breweryName, bottlecaps.capNumber ASC";

            $countryHasRegions = false;

            $capDataArray = MySQL::Cluster($sqlStatement);

            $tableHeader = 'Kronkorken mit fehlendem Bild';
            $alphaSortData = '';
            $sqlPager = '';
        }

   

        echo '<h2>Sammlung</h2>';

        echo '
            <center>
                '.((isset($_GET['sortbyletter']) OR isset($_GET['all'])) ? $alphaSort : '').'
                <br>
                '.$sqlPager.'
                <div class="bottlecapRowContainer">
                    <table class="capDisplay">
                        <tr>
                            <td colspan=5>'.$tableHeader.'</td>
                        </tr>
        ';

        $permissionCheck = CheckEditPermission();

        foreach($capDataArray AS $capData)
        {
            if(isset($_GET['noImage']))
            {
                if($capData['isSet'])
                {
                    $setFilepath = MySQL::Scalar("SELECT setFilepath FROM sets WHERE id = ?",'s',$capData['setID']);
                    $fileExist = file_exists("files/sets/".$capData['countryShort']."/".$setFilepath."/".$capData['capImage']);
                }
                else $fileExist = file_exists("files/bottlecaps/".$capData['countryShort']."/".$capData['breweryFilepath']."/".$capData['capImage']);

                if(!$fileExist) echo BottleCapRowData($capData, false, $countryHasRegions,$permissionCheck);
            }
            else echo BottleCapRowData($capData, false, $countryHasRegions,$permissionCheck);
        }

        echo '</table><div class="infoOverlays">';

        foreach($capDataArray AS $capData)
        {
            if(isset($_GET['noImage']))
            {
                if($capData['isSet'])
                {
                    $setFilepath = MySQL::Scalar("SELECT setFilepath FROM sets WHERE id = ?",'s',$capData['setID']);
                    $fileExist = file_exists("files/sets/".$capData['countryShort']."/".$setFilepath."/".$capData['capImage']);
                }
                else $fileExist = file_exists("files/bottlecaps/".$capData['countryShort']."/".$capData['breweryFilepath']."/".$capData['capImage']);

                if(!$fileExist) echo BottleCapRowInfoOverlay($capData,$permissionCheck);
            }
            else echo BottleCapRowInfoOverlay($capData,$permissionCheck);
        }




        echo '</div></div><br>'.$sqlPager.'</center>';

    }




	include("_footer.php");
?>
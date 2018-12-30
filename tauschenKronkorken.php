<?php
    require("_header.php");

//========================================================================================
//========================================================================================
//  START COUNTRY AND REGION SELECTION
//========================================================================================
//========================================================================================

    if(!isset($_GET['collection']))
    {
        if(isset($_GET['country']))
        {
            $country = $_GET['country'];
            $countryData = MySQL::Row("SELECT * FROM countries WHERE countryShort = ?",'s',$country);
            $pager = new Pager(20);
            $pager->SetColorSet(2);
            $pagerOffset = $pager->GetOffset();
            $pagerSize = $pager->GetPagerSize();

            $countryHasRegions = MySQL::Exist("SELECT breweries.id FROM breweries INNER JOIN countries ON countries.id = breweries.countryID INNER JOIN regions ON regions.id = breweries.regionID WHERE regions.countryID = ?",'i',$countryData['id']);

            echo '<h2 style="color: #1E90FF">Kronkorken aus '.$countryData['countryDE'].' tauschen</h2>';

            echo '<center>';

            echo '<a href="/tauschen/kronkorken/'.$country.'/alle">Alle Anzeigen</a><br><br>';

            $alphaSortData = MySQL::Cluster("SELECT *,LEFT(breweries.breweryName , 1) AS letter FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.isTradeable = '1' AND countries.countryShort = ? GROUP BY letter ORDER BY breweries.breweryName ASC",'s',$country);
            echo 'Nach Brauerei-Anfangsbuchstaben sortieren<br><div class="letterSelection">';
            foreach($alphaSortData AS $letter) echo '<a href="/tauschen/kronkorken/sammlung/'.$country.'/sortiert/'.$letter['letter'].'"><span>'.$letter['letter'].'</span></a>';
            echo '</div><br><br>';

            $sqlSelectStatement = "SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.ID WHERE bottlecaps.isTradeable = '1' AND breweries.countryID = ? GROUP BY breweries.breweryName ORDER BY breweryName ASC";

            echo 'Nach Brauerei sortieren';
            echo $pager->SQLAuto($sqlSelectStatement,'i',$countryData['id']);

            echo '<table class="breweryListTable">';
            $breweryList = MySQL::Cluster($sqlSelectStatement." LIMIT ?,?",'iii',$countryData['id'],$pagerOffset,$pagerSize);
            foreach($breweryList AS $brewery) echo BreweryListTile($brewery['id'],$countryHasRegions,true);
            echo '</table>';


            echo $pager->SQLAuto($sqlSelectStatement,'i',$countryData['id']);
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
        $pager->SetColorSet(2);

        if(isset($_GET['sortbyletter']))
        {
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
            AND bottlecaps.isTradeable = '1'
            AND breweries.breweryName LIKE ?
            AND isOwned = '1'
            ORDER BY breweries.breweryName, bottlecaps.capNumber ASC
            LIMIT $pagerOffset,$pagerSize";

            $capDataArray = MySQL::Cluster($sqlStatement,'@s',$country,$letter);
            $sqlPager = $pager->SQLAuto(str_replace(" LIMIT $pagerOffset,$pagerSize","",$sqlStatement),'@s',$country,$letter);
            $tableHeader = 'Kronkorken aus '.$capDataArray[0]['countryDE'].' beginnend mit "'.$_GET['letter'].'"';

            $alphaSortData = MySQL::Cluster("SELECT *,LEFT(breweries.breweryName , 1) AS letter FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.isTradeable = '1' AND countries.countryShort = ? GROUP BY letter ORDER BY breweries.breweryName ASC",'s',$country);
            $alphaSort = '<div class="letterSelection">';
            foreach($alphaSortData AS $letter) $alphaSort .= '<a href="/tauschen/kronkorken/sammlung/'.$country.'/sortiert/'.$letter['letter'].'"><span '.((isset($_GET['letter']) AND $_GET['letter'] == $letter['letter']) ? 'id="selected"' : '' ).'>'.$letter['letter'].'</span></a>';
            $alphaSort .= '</div>';
        }
        if(isset($_GET['sortbybrewery']))
        {
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
            AND bottlecaps.isTradeable = '1'
            AND breweries.breweryFilepath = ?
            AND isOwned = '1'
            ORDER BY breweries.breweryName, bottlecaps.capNumber ASC
            LIMIT $pagerOffset,$pagerSize";

            $capDataArray = MySQL::Cluster($sqlStatement,'@s',$country,$brewery);
            $sqlPager = $pager->SQLAuto(str_replace(" LIMIT $pagerOffset,$pagerSize","",$sqlStatement),'@s',$country,$brewery);
            $tableHeader = 'Kronkorken aus '.$capDataArray[0]['countryDE'].' der Brauerei "'.$capDataArray[0]['breweryName'].'"';
        }
        if(isset($_GET['all']))
        {
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
            AND bottlecaps.isTradeable = '1'
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
            foreach($alphaSortData AS $letter) $alphaSort .= '<a href="/tauschen/kronkorken/alle/'.$letter['letter'].'"><span '.((isset($_GET['letter']) AND $_GET['letter'] == $letter['letter']) ? 'id="selected"' : '' ).'>'.$letter['letter'].'</span></a>';
            $alphaSort .= '</div>';
        }

        echo '<h2 style="color: #1E90FF">Sammlung Tauschen</h2>';

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

        foreach($capDataArray AS $capData) echo BottleCapRowData($capData, false, $countryHasRegions,$permissionCheck,true);

        echo '</table><div class="infoOverlays">';

        foreach($capDataArray AS $capData) echo BottleCapRowInfoOverlay($capData,$permissionCheck);

        echo '</div></div><br>'.$sqlPager.'</center>';

        echo '<iframe src="/_iframe_addCapToCart" name="cartAddFrame" frameborder="0" hidden></iframe>';     
    }


    include("_footer.php");
?>
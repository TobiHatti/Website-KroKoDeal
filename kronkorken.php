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

   

        echo '<h2>Sammlung</h2>';

        echo '
            <center>
                '.(isset($_GET['sortbyletter']) ? $alphaSort : '').'
                <br>
                '.$sqlPager.'
                <div class="bottlecapRowContainer">
                    <table class="capDisplay">
                        <tr>
                            <td colspan=5>'.$tableHeader.'</td>
                        </tr>
        ';

        foreach($capDataArray AS $capData)
        {
            echo  '
                <tr>
                    <td>
                        <img src="/files/bottlecaps/'.$capData['countryShort'].'/'.$capData['breweryFilepath'].'/'.$capData['capImage'].'" alt="" />
                    </td>
                    <td>
                        <center>
                            <img src="/files/breweries/'.$capData['countryShort'].'/'.$capData['breweryImage'].'" alt="" />
                            <img src="/files/sidesigns/'.$capData['sidesignImage'].'" alt="" />
                            <br>
                            <img src="/content/blank.gif" class="flag flag-'.strtolower($capData['countryShort2']).'" id="flag_img"  alt="" />
                        </center>
                    </td>
                    <td>
                        <b>Brauerei:</b><br>
                        '.$capData['breweryName'].'<br><br>
                        <b>Name:</b><br>
                        '.$capData['name'].'<br><br>
                        <b>Sorte:</b>
                        <br>'.$capData['flavorDE'].'
                    </td>
                    <td>
                        <b>Land:</b>
                        '.$capData['countryDE'].' <br><br>
                        '.($countryHasRegions ? ('<b>Bundesland: </b>'.$capData['regionDE'].'<br><br>') : '').'
                        <b>Gekauft:</b><br>
                        '.$capData['locationAquired'].'<br><br>
                        <b>In Sammlung seit:</b><br>
                        '.$capData['dateAquired'].'
                    </td>
                    <td>
                        '.(($capData['breweryLink']!='') ? '<a target="_blank" href="'.$capData['breweryLink'].'"><button type="button"><i class="fas fa-home"></i> Zur Brauerei</button></a><br><br>' : '').'

                        <a href="#zusatzinfos'.$capData['bottlecapID'].'"><button type="button" onclick="bgenScroll();"><i class="fas fa-info-circle"></i> Zusatzinfos</button></a>
                    </td>
                </tr>
            ';
        }

        echo '</table><div class="infoOverlays">';

        foreach($capDataArray AS $capData)
        {
            echo '
                <div class="additionalInformationContainer" id="zusatzinfos'.$capData['bottlecapID'].'">
                    <div class="additionalInformationOverlay">
                        <table class="capData">
                            <tr>
                                <td rowspan=7>'.BottlecapColorScheme($capData['bottlecapCapColorShort'],$capData['bottlecapBaseColorValue'],$capData['bottlecapTextColorValue'],$capData['isUsed'],$capData['isTwistlock']).'</td>
                                <td>Kronkorkenfarbe: </td>
                                <td>'.$capData['bottlecapCapColorName'].'</td>

                                <td>Qualit&auml;t: </td>
                                <td>'.$capData['quality'].'</td>
                            </tr>
                            <tr>
                                <td>Grundfarbe: </td>
                                <td>'.$capData['bottlecapBaseColorName'].'</td>

                                <td>Auf Lager:</td>
                                <td>'.$capData['stock'].' St&uuml;ck</td>
                            </tr>
                            <tr>
                                <td>Textfarbe: </td>
                                <td>'.$capData['bottlecapTextColorName'].'</td>

                                <td>Tauschbar: </td>
                                <td>'.($capData['isTradeable'] ? 'Ja' : 'Nein').'</td>
                            </tr>
                            <tr>
                                <td><br></td>
                                <td><br></td>
                                <td><br></td>
                                <td><br></td>
                            </tr>
                            <tr>
                                <td>Set-Teil:</td>
                                <td>'.($capData['isSet'] ? 'Ja' : 'Nein').'</td>

                                <td>Randzeichen: </td>
                                <td>'.$capData['sidesignName'].'</td>
                            </tr>
                            <tr>
                                <td>Zustand: </td>
                                <td>'.($capData['isUsed'] ? 'Gebraucht' : 'Neu').'</td>

                                <td>KK-Nummer</td>
                                <td>'.$capData['capNumber'].'</td>
                            </tr>
                            <tr>
                                <td>Drehverschluss: </td>
                                <td>'.($capData['isTwistlock'] ? 'Ja' : 'Nein').'</td>

                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                        <a href="#"><div class="close" onclick="bgenScroll();">Schlie&szlig;en</div></a>
                    </div>
                </div>
            ';
        }

        echo '</div></div><br>'.$sqlPager.'</center>';

    }




	include("_footer.php");
?>
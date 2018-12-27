<?php

function BottlecapSingleBox($bottlecapID)
{
    $sqlStatement = "SELECT *,
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
    WHERE bottlecaps.id = ?";


    $row = MySQL::Row($sqlStatement,'s',$bottlecapID);

    $retval = '
    <div class="bottlecapSingleContainer">
        <table class="capDisplay">
            <tr>
                <td colspan=5>Neueste Kronkorken</td>
            </tr>
            <tr>
                <td>
                    <img src="/files/bottlecaps/'.$row['countryShort'].'/'.$row['breweryFilepath'].'/'.$row['capImage'].'" alt="" />

                </td>
                <td>
                    <center>
                        <img src="/files/breweries/'.$row['countryShort'].'/'.$row['breweryImage'].'" alt="" />
                        <img src="/files/sidesigns/'.$row['sidesignImage'].'" alt="" />
                        <br>
                        <img src="/content/blank.gif" class="flag flag-'.strtolower($row['countryShort2']).'" id="flag_img"  alt="" />
                    </center>
                </td>
                <td>
                    <b>Brauerei:</b><br>
                    '.$row['breweryName'].'<br><br>
                    <b>Name:</b><br>
                    '.$row['name'].'<br><br>
                    <b>Sorte:</b>
                    <br>'.$row['flavorDE'].'
                </td>
                <td>
                    <b>Land:</b><br>
                    '.$row['countryDE'].' <br><br>
                    <b>Gekauft:</b><br>
                    '.$row['locationAquired'].'<br><br>
                    <b>In Sammlung seit:</b><br>
                    '.$row['dateAquired'].'
                </td>
                <td>
                    '.(($row['breweryLink']!='') ? '<a target="_blank" href="'.$row['breweryLink'].'"><button type="button" class="cel_100"><i class="fas fa-home"></i> Zur Brauerei</button></a><br><br>' : '').'

                    <a href="#zusatzinfos" ><button type="button" class="cel_100"><i class="fas fa-info-circle"></i> Zusatzinfos</button></a>
                </td>
            </tr>


        </table>

        <div class="additionalInformationContainer" id="zusatzinfos">
            <div class="additionalInformationOverlay">
                <table class="capData">
                    <tr>
                        <td rowspan=7>'.BottlecapColorScheme($row['bottlecapCapColorShort'],$row['bottlecapBaseColorValue'],$row['bottlecapTextColorValue'],$row['isUsed'],$row['isTwistlock']).'</td>
                        <td>Kronkorkenfarbe: </td>
                        <td>'.$row['bottlecapCapColorName'].'</td>

                        <td>Qualit&auml;t: </td>
                        <td>'.$row['quality'].'</td>
                    </tr>
                    <tr>
                        <td>Grundfarbe: </td>
                        <td>'.$row['bottlecapBaseColorName'].'</td>

                        <td>Auf Lager:</td>
                        <td>'.$row['stock'].' St&uuml;ck</td>
                    </tr>
                    <tr>
                        <td>Textfarbe: </td>
                        <td>'.$row['bottlecapTextColorName'].'</td>

                        <td>Tauschbar: </td>
                        <td>'.($row['isTradeable'] ? 'Ja' : 'Nein').'</td>
                    </tr>
                    <tr>
                        <td><br></td>
                        <td><br></td>
                        <td><br></td>
                        <td><br></td>
                    </tr>
                    <tr>
                        <td>Set-Teil:</td>
                        <td>'.($row['isSet'] ? 'Ja' : 'Nein').'</td>

                        <td>Randzeichen: </td>
                        <td>'.$row['sidesignName'].'</td>
                    </tr>
                    <tr>
                        <td>Zustand: </td>
                        <td>'.($row['isUsed'] ? 'Gebraucht' : 'Neu').'</td>

                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Drehverschluss: </td>
                        <td>'.($row['isTwistlock'] ? 'Ja' : 'Nein').'</td>

                        <td></td>
                        <td></td>
                    </tr>
                </table>




                <a href="#"><div class="close">Schlie&szlig;en</div></a>
            </div>
        </div>
    ';

    return $retval;
}

function SetsAndA2ZButtons($ISOcode,$showBottlecapCount=false,$linkToCountries=false)
{
    $link = '';

    // A-Z Button
    if($linkToCountries) $link = '/kronkorken/'.$ISOcode;

    $retval = '
        '.($linkToCountries ? ('<a href="'.$link.'">') : '').'
            <div class="regionButtons setsAndA2ZButtons">
                <img src="/content/buttons/DE/a-z.png" alt="" />
    ';

    if($showBottlecapCount) $retval .= '<div>'.MySQL::Count("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.isCounted='1' AND countries.countryShort = ?",'s',$ISOcode).' St&uuml;ck</div>';

    $retval .= '</div>'.($linkToCountries ? '</a>' : '');

    // Set Button
    if($linkToCountries) $link = '/sets/'.$ISOcode;

    $retval .= '
        '.($linkToCountries ? ('<a href="'.$link.'">') : '').'
            <div class="regionButtons setsAndA2ZButtons">
                <img src="/content/buttons/DE/sets.png" alt="" />
    ';

    if($showBottlecapCount) $retval .= '<div>'.MySQL::Count("SELECT DISTINCT bottlecaps.setID FROM sets INNER JOIN bottlecaps ON sets.id = bottlecaps.setID INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.isCounted='1' AND countries.countryShort = ?",'s',$ISOcode).' Sets</div>';

    $retval .= '</div>'.($linkToCountries ? '</a>' : '');

    return $retval;
}

function ContinentButton($ISOcode,$showBottlecapCount=false,$linkToCountries=false)
{
    $link = '';

    if($linkToCountries) $link = '/laender/kontinent/'.$ISOcode;

    $retval = '
        '.($linkToCountries ? ('<a href="'.$link.'">') : '').'
            <div class="regionButtons continentButton">
                <img src="/content/regionButtons/DE/continent/'.$ISOcode.'.png" alt="" />
    ';

    if($showBottlecapCount)
    {
        $retval .= '
            <div>
                '.MySQL::Count("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id INNER JOIN continents ON countries.continentID = continents.id WHERE continents.continentShort = ?",'s',$ISOcode).' St&uuml;ck
            </div>
        ';
    }

    $retval .= '
            </div>
        '.($linkToCountries ? '</a>' : '').'
    ';

    return $retval;
}

function CountryButton($ISOcode,$showBottlecapCount=false, $showSetCount=false,$linkToCollectionOrSubmenu = false)
{
    $link = '';

    if($linkToCollectionOrSubmenu AND $showBottlecapCount)
    {
        if(MySQL::Exist("SELECT regions.id FROM regions INNER JOIN countries ON regions.countryID = countries.id WHERE countries.countryShort = ?",'s',$ISOcode)) $link = '/laender/regionen/'.$ISOcode;
        else $link = '/kronkorken/'.$ISOcode;
    }

    if($linkToCollectionOrSubmenu AND $showSetCount)
    {
        $link = '/sets/'.$ISOcode;
    }

    $retval = '
        '.($linkToCollectionOrSubmenu ? ('<a href="'.$link.'">') : '').'
            <div class="regionButtons countryButton">
                <img src="/content/regionButtons/DE/country/'.$ISOcode.'.png" alt="" />
    ';

    if($showBottlecapCount)
    {
        $retval .= '
            <div>
                '.MySQL::Count("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.isCounted='1' AND countries.countryShort = ?",'s',$ISOcode).' St&uuml;ck
            </div>
        ';
    }

    if($showSetCount)
    {
        $retval .= '
            <div>
                '.MySQL::Count("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE isSet = '1' AND countries.countryShort = ? GROUP BY setID",'s',$ISOcode).' Sets
            </div>
        ';
    }

    $retval .= '
            </div>
        '.($linkToCollectionOrSubmenu ? '</a>' : '').'
    ';

    return $retval;
}

function RegionButton($ISOcode,$showBottlecapCount=false,$linkToCollection = false)
{
    $link = '';

    if($linkToCollection)
    {
        $countryShort = MySQL::Scalar("SELECT countries.countryShort FROM regions INNER JOIN countries ON regions.countryID = countries.id WHERE regions.regionShort = ?",'s',$ISOcode);
        $link = '/kronkorken/'.$countryShort.'/'.$ISOcode;
    }

    $retval = '
        '.($linkToCollection ? ('<a href="'.$link.'">') : '').'
            <div class="regionButtons federalButton">
                <img src="/content/regionButtons/DE/region/'.$ISOcode.'.png" alt="" />
    ';

    if($showBottlecapCount)
    {
        $retval .= '
            <div>
                '.MySQL::Count("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.ID INNER JOIN regions ON breweries.regionID = regions.id WHERE bottlecaps.isCounted = '1' AND regions.regionShort = ?",'s',$ISOcode).' St&uuml;ck
            </div>
        ';
    }

    $retval .= '
            </div>
        '.($linkToCollection ? '</a>' : '').'
    ';

    return $retval;
}




function BottlecapColorScheme($capColorCode,$baseColor,$textColor,$isUsed = false,$isTwistlock = false)
{
    $retval = '

        <div class="bottlecapColorSchemeContainer">
            <div><img id="'.$capColorCode.'" src="/content/cap'.($isUsed ? 'Used' : 'New').'Colored.png" alt="" /></div>
            <div style="background: #'.$baseColor.'"></div>
            <div style="color: #'.$textColor.'">K-K-D</div>
            '.($isTwistlock ? '<div></div>' : '').'
        </div>

    ';

    return $retval;
}

function BreweryListTile($breweryID,$showRegional=false)
{
    if($showRegional) $breweryData = MySQL::Row("SELECT *,breweries.id AS breweryID FROM breweries INNER JOIN countries ON breweries.countryID = countries.id INNER JOIN regions ON breweries.regionID = regions.id WHERE breweries.id = ? ORDER BY breweries.breweryName ASC",'i',$breweryID);
    else $breweryData = MySQL::Row("SELECT *,breweries.id AS breweryID FROM breweries INNER JOIN countries ON breweries.countryID = countries.id  WHERE breweries.id = ? ORDER BY breweries.breweryName ASC",'i',$breweryID);

    $bottleCapCount = MySQL::Count("SELECT * FROM bottlecaps WHERE isCounted = '1' AND isSet = '0' AND breweryID = ?",'i',$breweryData['breweryID']);
    $tradeableCount = MySQL::Count("SELECT * FROM bottlecaps WHERE isCounted = '1' AND isSet = '0' AND isTradeable = '1' AND breweryID = ?",'i',$breweryData['breweryID']);


    $retval = '
        <tr>
            <td><img src="/files/breweries/'.$breweryData['countryShort'].'/'.$breweryData['breweryImage'].'" alt="" /></td>
            <td>
                <i>Brauerei:</i> '.$breweryData['breweryName'].'<br><br>
                <i>Land:</i> '.$breweryData['countryDE'].'<br><br>
                '.($showRegional ? ('<i>Bundesland:</i> '.$breweryData['regionDE']) : '').'
            </td>
            <td>
                <i>Kronkorken:</i> '.$bottleCapCount.'<br>
                <i>Tauschbar:</i> '.$tradeableCount.'
            </td>
            <td>
                '.(($breweryData['breweryLink']!='') ? '<a target="_blank" href="'.$breweryData['breweryLink'].'"><button type="button" class="cel_100"><i class="fas fa-home"></i> Zur Brauerei</button></a><br><br>' : '').'

                <a href="/kronkorken/sammlung/'.$breweryData['countryShort'].'/brauerei/'.$breweryData['breweryFilepath'].'"><button type="button" class="cel_100">Kronkorken dieser<br>Brauerei</button></a>
            </td>
        </tr>
    ';

    return $retval;
}

function SetTile($setID,$isEditMode = false)
{
    $setData = MySQL::Row("SELECT * FROM sets INNER JOIN bottlecaps ON sets.id = bottlecaps.setID INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE sets.id = ?",'i',$setID);

    $setThumbnail = MySQL::Scalar("SELECT capImage FROM bottlecaps WHERE id = ?",'i',$setData['thumbnailID']);

    $retval = '
        <table class="setTileTable">
            <tr><td colspan=3>'.$setData['setName'].'</td></tr>
            <tr>
                <td><img src="/files/sets/'.$setData['countryShort'].'/'.$setData['setFilepath'].'/'.$setThumbnail.'" alt="" /></td>
                <td>
                    <b>Brauerei: </b> '.$setData['breweryName'].'<br><br>
                    <b>Set-Gr&ouml;&szlig;e: </b>'.$setData['setSize'].'<br><br>
                    <b>Land: </b>'.$setData['countryDE'].'
                </td>
                <td>
                ';

                if($isEditMode)
                {
                    $retval .= '
                        <a href="/bearbeiten/set/'.$setData['setID'].'"><button type="button" class="cel_100 cel_h25" style="margin-bottom: 5px; background: #32CD32">Bearbeiten</button></a><br>
                        <a href="/entfernen/set/'.$setData['setID'].'"><button type="button" class="cel_100 cel_h25" style="margin-bottom: 5px; background: #D60000">L&ouml;schen</button></a><br><br>
                    ';

                    $retval .= '<a href="/sets/'.$setData['countryShort'].'/'.$setData['setFilepath'].'"><button type="button" class="cel_100">Set betrachten</button></a>';
                }
                else
                {
                    $retval .= '<a href="/sets/'.$setData['countryShort'].'/'.$setData['setFilepath'].'"><button type="button" class="cel_100">Set betrachten</button></a>';
                }

                echo '


                </td>
            </tr>
        </table>
    ';

    return $retval;
}

function BottleCapRowData($capData, $isSet, $countryHasRegions,$isEditMode = false)
{
    if($isSet) $imagePath = '/files/sets/'.$capData['countryShort'].'/'.$capData['setFilepath'].'/'.$capData['capImage'];
    else $imagePath = '/files/bottlecaps/'.$capData['countryShort'].'/'.$capData['breweryFilepath'].'/'.$capData['capImage'];


    $retval = '
        <tr>
            <td>
                '.($isSet ? '<a name="cap'.$capData['bottlecapID'].'">' : '').'
                <img src="'.$imagePath.'" alt="" />
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
                '.($isSet ? ('<br><br><b>In Sammlung: </b> '.($capData['isOwned'] ? 'Ja' : 'Nein' )) : '').'
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
    ';

    if($isEditMode)
    {
        $retval .= '
            <a href="/bearbeiten/kronkorken/'.$capData['bottlecapID'].'"><button type="button" class="cel_100 cel_h25" style="margin-bottom: 5px; background: #32CD32">Bearbeiten</button></a><br>
            <a href="/entfernen/kronkorken/'.$capData['bottlecapID'].'"><button type="button" class="cel_100 cel_h25" style="margin-bottom: 5px; background: #D60000">L&ouml;schen</button></a><br><br>
        ';

        $retval .= '
            '.(($capData['breweryLink']!='') ? '<a target="_blank" href="'.$capData['breweryLink'].'"><button type="button" class="cel_100 cel_h25" style="margin-bottom: 5px;"><i class="fas fa-home"></i> Zur Brauerei</button></a><br>' : '').'
            <a href="#zusatzinfos'.$capData['bottlecapID'].'"><button type="button" onclick="bgenScroll();" class="cel_100 cel_h25" style="margin-bottom: 5px;"><i class="fas fa-info-circle"></i> Zusatzinfos</button></a>
        ';
    }
    else
    {
        $retval .= '
            '.(($capData['breweryLink']!='') ? '<a target="_blank" href="'.$capData['breweryLink'].'"><button type="button" class="cel_100"><i class="fas fa-home"></i> Zur Brauerei</button></a><br><br>' : '').'
            <a href="#zusatzinfos'.$capData['bottlecapID'].'"><button type="button" onclick="bgenScroll();" class="cel_100"><i class="fas fa-info-circle"></i> Zusatzinfos</button></a>
        ';
    }

    $retval .= '
            </td>
        </tr>
    ';

    return $retval;
}

function BottleCapRowInfoOverlay($capData)
{


    $retval = '
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

    return $retval;
}

function CheckEditPermission()
{
    $rank = MySQL::Scalar("SELECT rank FROM users WHERE id = ?",'s',$_SESSION['userID']);
    if($rank>=95) return true;
    else return false;
}

?>
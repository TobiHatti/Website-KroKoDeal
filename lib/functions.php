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
                    <a target="_blank" href="'.$row['breweryLink'].'"><button type="button"><i class="fas fa-home"></i> Zur Brauerei</button></a><br><br>

                    <a href="#zusatzinfos"><button type="button"><i class="fas fa-info-circle"></i> Zusatzinfos</button></a>
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

function CountryButton($ISOcode,$showBottlecapCount=false)
{
    $retval = '
        <div class="regionButtons countryButton">
            <img src="/content/regionButtons/DE/country/'.$ISOcode.'.png" alt="" />
    ';

    if($showBottlecapCount)
    {
        $retval .= '
            <div>
                '.MySQL::Count("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.isOwned='1' AND countries.countryShort = '$ISOcode'").' St&uuml;ck
            </div>
        ';
    }

    $retval .= '</div>';

    return $retval;
}

function ContinentButton($ISOcode,$showBottlecapCount=false)
{
    $retval = '
        <div class="regionButtons continentButton">
            <img src="/content/regionButtons/DE/continent/'.$ISOcode.'.png" alt="" />
    ';

    if($showBottlecapCount)
    {
        $retval .= '
            <div>
                '.MySQL::Count("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id INNER JOIN continents ON countries.continentID = continents.id WHERE bottlecaps.isOwned='1' AND continents.continentShort = '$ISOcode'").' St&uuml;ck
            </div>
        ';
    }

    $retval .= '</div>';

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


?>
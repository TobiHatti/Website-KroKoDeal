<?php


function BottlecapSingleBox($bottlecapID)
{
    $sqlStatement = "SELECT * FROM bottlecaps
    INNER JOIN breweries ON bottlecaps.breweryID = breweries.id
    INNER JOIN countries ON breweries.countryID = countries.id
    INNER JOIN flavors ON bottlecaps.flavorID = flavors.id
    WHERE bottlecaps.id = ?";


    $row = MySQL::Row($sqlStatement,'s',$bottlecapID);

    $retval = '
    <div class="bottlecapSingleContainer">
        <table>
            <tr>
                <td colspan=5>Neueste Kronkorken</td>
            </tr>
            <tr>
                <td>
                    <img src="/files/bottlecaps/'.$row['countryShort'].'/'.$row['breweryFilepath'].'/'.$row['capImage'].'" alt="" />
                    <img src="/content/blank.gif" class="flag flag-ca" id="flag_img"  alt="" />
                </td>
                <td>
                    <center>
                        <img src="/files/breweries/'.$row['countryShort'].'/'.$row['breweryImage'].'" alt="" />
                        <img src="/files/sidesigns/#" alt="" />

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
                    '.$row['countryDE'].'<br><br>
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
                <a href="#">
                    <div class="close">
                        Schlie&szlig;en
                    </div>
                </a>
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


?>
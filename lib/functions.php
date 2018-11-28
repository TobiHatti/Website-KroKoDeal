<?php


function BottlecapSingleBox($bottlecapID)
{
    $row = MySQL::Row("SELECT *,countries.short AS country, breweries.filepath AS brewery, bottlecaps.image AS capImage, breweries.image AS breweryImage FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.ID INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.id = '$bottlecapID'");

    $retval = '
        <table>
            <tr>
                <td colspan=5>Neueste Kronkorken</td>
            </tr>
            <tr>
                <td>
                    <img src="/files/bottlecaps/'.$row['country'].'/'.$row['brewery'].'/'.$row['capImage'].'" alt="" />
                </td>
                <td>
                    <img src="/files/breweries/'.$row['country'].'/'.$row['breweryImage'].'" alt="" />
                    <img src="/files/sidesigns/#" alt="" />
                </td>
            </tr>
        </table>
    ';

    return $retval;
}


?>
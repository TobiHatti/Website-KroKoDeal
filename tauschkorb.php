<?php
    require("_header.php");

    if(isset($_SESSION['userID']))
    {
        echo '<h2>Tausch-Korb</h2>';

        $cartCount = MySQL::Count("SELECT id FROM cart WHERE userID = ? AND tradeConfirmed = 0 AND tradeCompleted = 0",'s',$_SESSION['userID']);

        if($cartCount == 0) echo '<br><br><h3>Ihr Tausch-Korb ist leer!</h3>';
        else
        {
            echo '<h3>'.$cartCount.' gegenst&auml;nde aktuell in Ihrem Tausch-Korb</h3>';

            $cartData = MySQL::Cluster("SELECT * FROM cart WHERE userID = ? AND tradeConfirmed = 0 AND tradeCompleted = 0",'s',$_SESSION['userID']);

            echo '<center>';

            echo '<br><button type="button" style="background: #32CD32" class="cel_xl cel_f15">Tauschanfrage stellen</button>';

            foreach($cartData AS $cartItem)
            {
                if($cartItem['isSet'])
                {
                    $setData = MySQL::Row("SELECT * FROM sets INNER JOIN bottlecaps ON sets.id = bottlecaps.setID INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id INNER JOIN flavors ON bottlecaps.flavorID = flavors.id INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id WHERE sets.id = ?",'s',$cartItem['objID']);

                    $capName = $setData['setName'];
                    $imagePath = '/files/sets/'.$setData['countryShort'].'/'.$setData['setFilepath'].'/'.$setData['capImage'];

                    echo '
                        <table class="capCartDisplay">
                            <tr>
                                <td>
                                    <div>Set</div>
                                    <img src="'.$imagePath.'" alt="" />
                                </td>
                                <td>
                                    <center>
                                        <img src="/files/breweries/'.$setData['countryShort'].'/'.$setData['breweryImage'].'" alt="" />
                                        <img src="/files/sidesigns/'.$setData['sidesignImage'].'" alt="" />
                                        <br>
                                        <img src="/content/blank.gif" class="flag flag-'.strtolower($setData['countryShort2']).'" id="flag_img"  alt="" />
                                    </center>
                                </td>
                                <td>
                                    <b>Brauerei:</b> '.$setData['breweryName'].'<br><br>
                                    <b>Set-Name:</b> '.$capName.'<br><br>
                                    <b>Sorte:</b> '.$setData['flavorDE'].'

                                </td>
                                <td>
                                    <b>Land:</b>'.$setData['countryDE'].' <br><br>
                                    <b>Gr&ouml;&szlig;e:</b> '.$setData['setSize'].'<br><br>
                                    <b>Qualit&auml;t: </b> '.(($setData['quality'] != '') ? $setData['quality'] : '<span title="Keine Angaben">K.A.</span>').'
                                </td>
                                <td>
                                    <a href="/entfernen/tauschkorb/'.$cartItem['id'].'"><button type="button" style="background: #CC0000">Gegenstand entfernen</button></a>
                                </td>
                            </tr>
                        </table>
                    ';

                }
                else
                {
                    $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id INNER JOIN flavors ON bottlecaps.flavorID = flavors.id INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id WHERE bottlecaps.id = ?",'s',$cartItem['objID']);

                    if($capData['isSet'])
                    {
                        $setData = MySQL::Row("SELECT * FROM sets WHERE id = ?",'s',$capData['setID']);
                        $capName = $setData['setName'].' - '.$capData['name'];
                        $imagePath = '/files/sets/'.$capData['countryShort'].'/'.$setData['setFilepath'].'/'.$capData['capImage'];
                    }
                    else
                    {
                        $capName = $capData['name'];
                        $imagePath = '/files/bottlecaps/'.$capData['countryShort'].'/'.$capData['breweryFilepath'].'/'.$capData['capImage'];
                    }

                    echo '
                        <table class="capCartDisplay">
                            <tr>
                                <td>
                                    <div>Kronkorken</div>
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
                                    <b>Brauerei:</b> '.$capData['breweryName'].'<br><br>
                                    <b>Name:</b> '.$capName.'<br><br>
                                    <b>Sorte:</b> '.$capData['flavorDE'].'<br><br>
                                    <b>Qualit&auml;t: </b> '.(($capData['quality'] != '') ? $capData['quality'] : '<span title="Keine Angaben">K.A.</span>').'
                                </td>
                                <td>
                                    <b>Land:</b>
                                    '.$capData['countryDE'].' <br><br>
                                    <b>Gekauft:</b><br>
                                    '.$capData['locationAquired'].'<br><br>
                                    <b>In Sammlung seit:</b><br>
                                    '.$capData['dateAquired'].'
                                </td>
                                <td>
                                    <a href="/entfernen/tauschkorb/'.$cartItem['id'].'"><button type="button" style="background: #CC0000">Gegenstand entfernen</button></a>
                                </td>
                            </tr>
                        </table>
                    ';
                }
            }

            echo '</center>';
        }
    }
    else Page::Redirect("/sign-in");

    include("_footer.php");
?>
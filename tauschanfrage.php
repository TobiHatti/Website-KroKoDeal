<?php
	require("_header.php");

    if(isset($_POST['createTradeRequest']))
    {
        $tradeID = uniqid();

        MySQL::NonQuery("UPDATE cart SET tradeID = ? WHERE tradeID = '' AND userID = ?",'ss',$tradeID,$_SESSION['userID']);

        $tradeMessage = $_POST['tradeMessage'];
        $sendConfirmation = isset($_POST['sendConfirmation']) ? 1 : 0;
        $requestedDate = date("Y-m-d");

        MySQL::NonQuery("INSERT INTO trades (id,userID,dateTradeRequested,tradeMessage,mailNotifications) VALUES (?,?,?,?,?)",'sssss',$tradeID,$_SESSION['userID'],$requestedDate,$tradeMessage,$sendConfirmation);


        $tradeMessageTable = '
            <table style="background: #FFFFFF; border: 1px solid black; width: 100%;">
                <tr style="background: #FF8C00">
                    <td style="border: 1px solid black; line-height: 20px; padding: 1px; height: 20px; text-align: center;">Pos.</td>
                    <td style="border: 1px solid black; line-height: 20px; padding: 1px; height: 20px; text-align: center;">Typ</td>
                    <td style="border: 1px solid black; line-height: 20px; padding: 1px; height: 20px; text-align: center;">Land</td>
                    <td style="border: 1px solid black; line-height: 20px; padding: 1px; height: 20px; text-align: center;">Brauerei</td>
                    <td style="border: 1px solid black; line-height: 20px; padding: 1px; height: 20px; text-align: center;">Name</td>
                    <td style="border: 1px solid black; line-height: 20px; padding: 1px; height: 20px; text-align: center;">Kapsel-Nummer</td>
                    <td style="border: 1px solid black; line-height: 20px; padding: 1px; height: 20px; text-align: center;">St&uuml;ckzahl</td>
                    <td style="border: 1px solid black; line-height: 20px; padding: 1px; height: 20px; text-align: center;">Qual.</td>
                </tr>
        ';

        $i=1;
        $tradeData = MySQL::Cluster("SELECT * FROM cart WHERE userID = ? AND tradeID = ?",'ss',$_SESSION['userID'],$tradeID);
        foreach($tradeData AS $tradeElement)
        {
            if($tradeElement['isSet'])
            {
                $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN sets ON bottlecaps.setID = sets.id INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE sets.id = ? AND bottlecaps.id = sets.thumbnailTradeID",'s',$tradeElement['objID']);

                $tradeMessageTable .= '
                    <tr>
                        <td style="border: 1px solid black; text-align: center;">'.$i++.'</td>
                        <td style="border: 1px solid black;">Set</td>
                        <td style="border: 1px solid black;">'.$capData['countryShort'].' - '.$capData['countryDE'].'</td>
                        <td style="border: 1px solid black;">'.$capData['breweryName'].'</td>
                        <td style="border: 1px solid black;">'.$capData['setName'].'</td>
                        <td style="border: 1px solid black; text-align: center;">-</td>
                        <td style="border: 1px solid black; text-align: center;">'.$capData['setSize'].' Stk.</td>
                        <td style="border: 1px solid black; text-align: center;">'.($capData['quality'] == "" ? 'n/a' : $capData['quality']).'</td>
                    </tr>
                ';
            }
            else
            {
                $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.id = ?",'s',$tradeElement['objID']);
                $setData = MySQL::Row("SELECT * FROM sets WHERE id = ?",'s',$capData['setID']);
                
                if($capData['isSet']) $capName = $setData['setName'].' - '.$capData['name'];
                else $capName = $capData['name'];

                $tradeMessageTable .= '
                    <tr>
                        <td style="border: 1px solid black; text-align: center;">'.$i++.'</td>
                        <td style="border: 1px solid black;">Kronkorken</td>
                        <td style="border: 1px solid black;">'.$capData['countryShort'].' - '.$capData['countryDE'].'</td>
                        <td style="border: 1px solid black;">'.$capData['breweryName'].'</td>
                        <td style="border: 1px solid black;">'.$capName.'</td>
                        <td style="border: 1px solid black; text-align: center;">'.$capData['capNumber'].'</td>
                        <td style="border: 1px solid black; text-align: center;">-</td>
                        <td style="border: 1px solid black; text-align: center;">'.($capData['quality'] == "" ? 'n/a' : $capData['quality']).'</td>
                    </tr>
                ';
            }
        }
        $tradeMessageTable .= '</table>';

        $userData = MySQL::Row("SELECT * FROM users WHERE id = ?",'s',$_SESSION['userID']);

        if($sendConfirmation)
        {
            // Message to trader
            $traderMessage = "Dies ist die Best&auml;tigung Ihrer Tauschanfrage bei Kro-Ko-Deal.com<br><br>Wir melden sich bei Ihnen sobald der Tausch seitens des Tauschpartners akzeptiert wurde!<br><h3>Ihre Tauschanfrage:</h3><br>".nl2br($tradeMessage).'<br><br>'.$tradeMessageTable;
            $traderMessage = MailFormater(StringOp::GermanSpecialChars($traderMessage),'Best&auml;tigung Ihrer Tauschanfrage bei Kro-Ko-Deal');
            SendEMail($userData['email'],'Best&auml;tigung Ihrer Tauschanfrage bei Kro-Ko-Deal',$traderMessage);
        }

        // Message to Owner
        $ownerMessage = "Der Nutzer ".$userData['firstName']." ".$userData['lastName']." / ".$userData['username']."  (".$userData['email'].") hat soeben eine Tauschanfrage gestellt:<br><h3>Tauschanfrage:</h3><br>".nl2br($tradeMessage).'<br><br>'.$tradeMessageTable;
        $ownerMessage = MailFormater(StringOp::GermanSpecialChars($ownerMessage),'Neue Tauschanfrage bei Kro-Ko-Deal');
        SendEMail("trade@kro-ko-deal.com",'Neue Tauschanfrage bei Kro-Ko-Deal',$ownerMessage);

        Page::Redirect("/tauschanfrage?confirmed");
        die();
    }

    if(isset($_SESSION['userID']))
    {
        if(isset($_GET['confirmed']))
        {
            echo '<h2>Tauschanfrage gesendet!</h2><br>';

            echo '
                <p>
                    Sie erhalten eine Tausch-Best&auml;tigung sobald der Tausch seitens des Tausch-Partners akzeptiert wurde!
                </p>
                <a href="/"><button type="button">Zur&uuml;ck zur Startseite</button></a>
            ';
        }
        else
        {
            echo '<h2>Tauschanfrage stellen</h2><br>';

            echo '
                <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <center>
                        <table class="tradeRequestTable">
                            <tr>
                                <td>
                                    ';
                                    $bottlecapCount = 0;
                                    $singleCapCount = 0;
                                    $setCapCount = 0;
                                    $setCount = 0;
                                    $i=1;
                                    $tradeCapDetailList = '';
                                    $tradeData = MySQL::Cluster("SELECT * FROM cart WHERE userID = ? AND tradeID = ''",'s',$_SESSION['userID']);
                                    foreach($tradeData AS $tradeElement)
                                    {
                                        if($tradeElement['isSet'])
                                        {
                                            $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN sets ON bottlecaps.setID = sets.id INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE sets.id = ? AND bottlecaps.id = sets.thumbnailTradeID",'s',$tradeElement['objID']);

                                            $imagePath = '/files/sets/'.$capData['countryShort'].'/'.$capData['setFilepath'].'/'.($capData['capImageTrade'] == "" ? $capData['capImage'] : $capData['capImageTrade']);

                                            $bottlecapCount += $capData['setSize'];
                                            $setCapCount += $capData['setSize'];
                                            $setCount++;

                                            $tradeCapDetailList .= '
                                                <tr>
                                                    <td>'.$i++.'</td>
                                                    <td>Set</td>
                                                    <td>'.$capData['countryShort'].' - '.$capData['countryDE'].'</td>
                                                    <td>'.$capData['breweryName'].'</td>
                                                    <td>'.$capData['setName'].'</td>
                                                    <td>-</td>
                                                    <td>'.$capData['setSize'].' Stk.</td>
                                                    <td>'.($capData['quality'] == "" ? 'n/a' : $capData['quality']).'</td>
                                                </tr>
                                            ';
                                        }
                                        else
                                        {
                                            $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.id = ?",'s',$tradeElement['objID']);

                                            if($capData['isSet'])
                                            {
                                                $setData = MySQL::Row("SELECT * FROM sets WHERE id = ?",'s',$capData['setID']);
                                                $imagePath = '/files/sets/'.$capData['countryShort'].'/'.$setData['setFilepath'].'/'.($capData['capImageTrade'] == "" ? $capData['capImage'] : $capData['capImageTrade']);
                                                $capName = $setData['setName'].' - '.$capData['name'];
                                            }
                                            else
                                            {
                                                $imagePath = '/files/bottlecaps/'.$capData['countryShort'].'/'.$capData['breweryFilepath'].'/'.($capData['capImageTrade'] == "" ? $capData['capImage'] : $capData['capImageTrade']);
                                                $capName = $capData['name'];
                                            }

                                            $bottlecapCount++;
                                            $singleCapCount++;

                                            $tradeCapDetailList .= '
                                                <tr>
                                                    <td>'.$i++.'</td>
                                                    <td>Kronkorken</td>
                                                    <td>'.$capData['countryShort'].' - '.$capData['countryDE'].'</td>
                                                    <td>'.$capData['breweryName'].'</td>
                                                    <td>'.$capName.'</td>
                                                    <td>'.$capData['capNumber'].'</td>
                                                    <td>-</td>
                                                    <td>'.($capData['quality'] == "" ? 'n/a' : $capData['quality']).'</td>
                                                </tr>
                                            ';
                                        }


                                        echo '
                                            <div>
                                                <img src="'.$imagePath.'"/>
                                                <img src="/content/blank.gif" class="flag flag-'.strtolower($capData['countryShort2']).'" id="flag_img"  alt="" />

                                        ';

                                        if($tradeElement['isSet'])
                                        {
                                            echo '<span>Set<b> - Set - </b>Set</span><i>'.$capData['setSize'].' St&uuml;ck</i>';
                                        }

                                        echo '</div>';
                                    }

                                    echo '
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h3>Infos zum Tausch:</h3>
                                    Kronkorken gesammt: <b>'.$bottlecapCount.' St&uuml;ck</b><br>
                                    '.($setCount != 0 ? 'Sets gesammt: <b>'.$setCount.' St&uuml;ck</b><br>' : '').'
                                    '.($setCapCount != 0 ? '&bullet; Davon <b>'.$setCapCount.'</b> aus Sets und <b>'.$singleCapCount.'</b> einzeln<br>' : '').'

                                    <br>
                                    <table class="tradeRequestCapDataTable">
                                        <tr>
                                            <td>Pos.</td>
                                            <td>Typ</td>
                                            <td>Land</td>
                                            <td>Brauerei</td>
                                            <td>Name</td>
                                            <td>Kapsel-Nummer</td>
                                            <td>St&uuml;ckzahl</td>
                                            <td>Qual.</td>
                                        </tr>
                                        '.$tradeCapDetailList.'
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Nachricht hinzuf&uuml;gen (optional):<br>
                                    <textarea style="width: 100%; height: 90px;" name="tradeMessage" placeholder="Nachricht zum Tausch hinzuf&uuml;gen (optional)..."></textarea>
                                    '.Tickbox("sendConfirmation","sendConfirmation",'&nbsp;&nbsp;Tausch-Informationen per E-Mail senden (<span style="color: #1E90FF">'.MySQL::Scalar("SELECT email FROM users WHERE id = ?",'s',$_SESSION['userID']).'</span>)',true).'
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button name="createTradeRequest" type="submit" style="background: #32CD32">Tauschanfrage absenden</button>
                                </td>
                            </tr>
                        </table>
                    </center>
                </form>
            ';
        }
    }

	include("_footer.php");
?>
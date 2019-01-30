<?php
	require("_header.php");

    NavBar("Home","Tauschen");

    if(isset($_POST['confirmTrade']))
    {
        $tradeID = $_POST['confirmTrade'];
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $tradeMessage = $_POST['message'];

        MySQL::NonQuery("UPDATE trades SET tradeConfirmed = '1', dateTradeConfirmed = ? WHERE id = ?",'ss',$date,$tradeID);

        $tradePartnerID = MySQL::Scalar("SELECT userID FROM trades WHERE id = ?",'s',$tradeID);
        $ownerID = MySQL::Scalar("SELECT id FROM users WHERE rank = '99'");

        $userData = MySQL::Row("SELECT * FROM users WHERE id = ?",'s',$tradePartnerID);

        Message($ownerID, $tradePartnerID, "Ihre Tauschanfrage wurde best&auml;tigt!", $tradeID,true);
        Message($ownerID, $tradePartnerID, $tradeMessage, $tradeID);

        if(MySQL::Scalar("SELECT mailNotifications FROM trades WHERE id = ?",'s',$tradeID) == '1')
        {
            $traderMessage = "<h3>Ihre Tauschanfrage wurde best&auml;tigt!</h3><br>".nl2br($tradeMessage);
            $traderMessage = MailFormater(StringOp::GermanSpecialChars($traderMessage),'Ihre Tauschanfrage wurde best&auml;tigt');
            SendEMail($userData['email'],'Ihre Tauschanfrage wurde best&auml;tigt',$traderMessage);
        }

        Page::Redirect(Page::This());
        die();
    }

    if(isset($_POST['completeTrade']))
    {
        $tradeID = $_POST['completeTrade'];
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $tradeMessage = $_POST['message'];

        MySQL::NonQuery("UPDATE trades SET tradeCompleted = '1', dateTradeCompleted = ? WHERE id = ?",'ss',$date,$tradeID);

        $tradePartnerID = MySQL::Scalar("SELECT userID FROM trades WHERE id = ?",'s',$tradeID);
        $ownerID = MySQL::Scalar("SELECT id FROM users WHERE rank = '99'");

        $userData = MySQL::Row("SELECT * FROM users WHERE id = ?",'s',$tradePartnerID);

        Message($ownerID, $tradePartnerID, "Ihr Tausch wurde abgeschlossen!", $tradeID,true);
        Message($ownerID, $tradePartnerID, $tradeMessage, $tradeID);


        $tradeDataArray = MySQL::Cluster("SELECT * FROM cart WHERE tradeID = ?",'s',$tradeID);
        foreach($tradeDataArray as $tradeData)
        {
            if($tradeData['isSet']!='1') MySQL::NonQuery("UPDATE bottlecaps SET stock = stock - 1 WHERE id = ?",'s',$tradeData['objID']);
            else MySQL::NonQuery("UPDATE bottlecaps SET stock = stock - 1 WHERE setID = ?",'s',$tradeData['objID']);
        }



        if(MySQL::Scalar("SELECT mailNotifications FROM trades WHERE id = ?",'s',$tradeID) == '1')
        {
            $traderMessage = "<h3>Ihr Tausch auf Kro-Ko-Deal wurde abgeschlossen!</h3><br>".nl2br($tradeMessage);
            $traderMessage = MailFormater(StringOp::GermanSpecialChars($traderMessage),'Ihr Tausch auf Kro-Ko-Deal wurde abgeschlossen');
            SendEMail($userData['email'],'Ihr Tausch auf Kro-Ko-Deal wurde abgeschlossen',$traderMessage);
        }

        Page::Redirect(Page::This());
        die();
    }

    if(isset($_POST['reactivateTrade']))
    {
        $tradeID = $_POST['reactivateTrade'];
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $tradeMessage = $_POST['message'];

        MySQL::NonQuery("UPDATE trades SET tradeCompleted = '0' WHERE id = ?",'s',$tradeID);

        $tradePartnerID = MySQL::Scalar("SELECT userID FROM trades WHERE id = ?",'s',$tradeID);
        $ownerID = MySQL::Scalar("SELECT id FROM users WHERE rank = '99'");

        $userData = MySQL::Row("SELECT * FROM users WHERE id = ?",'s',$tradePartnerID);

        Message($ownerID, $tradePartnerID, "Ihr Tausch wurde wiederbet&auml;tigt!", $tradeID,true);
        Message($ownerID, $tradePartnerID, $tradeMessage, $tradeID);

        if(MySQL::Scalar("SELECT mailNotifications FROM trades WHERE id = ?",'s',$tradeID) == '1')
        {
            $traderMessage = "<h3>Ihr Tausch auf Kro-Ko-Deal wurde wiederbet&auml;tigt!</h3><br>".nl2br($tradeMessage);
            $traderMessage = MailFormater(StringOp::GermanSpecialChars($traderMessage),'Ihr Tausch auf Kro-Ko-Deal wurde wiederbet&auml;tigt');
            SendEMail($userData['email'],'Ihr Tausch auf Kro-Ko-Deal wurde wiederbet&auml;tigt',$traderMessage);
        }

        Page::Redirect(Page::This());
        die();
    }



    if(isset($_GET['section']))
    {
        if($_GET['section']=='laender')
        {
            NavBar("Home","Tauschen","TradeCountries");

            echo '<h2 style="color: #1E90FF">Kronkorken Tauschen</h2><br>';

            echo '<center>';

            $rows = MySQL::Cluster("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE isTradeable = '1' GROUP BY countries.countryShort");
            foreach($rows AS $row) echo TradeCountryButton($row['countryShort'],true,false,true);

            echo '</center>';
        }

        if($_GET['section']=='sets')
        {
            NavBar("Home","Tauschen","TradeSets");

            echo '<h2 style="color: #1E90FF">Sets Tauschen</h2>';

            echo '<center>';
            $sqlStatement = "SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE isSet = '1' AND isTradeable = '1' AND stock > '0' GROUP BY countries.countryShort";
            $buttonArray = MySQL::Cluster($sqlStatement);
            foreach($buttonArray AS $button) echo TradeCountryButton($button['countryShort'],false,true,true);
            echo '</center>';

            if(!MySQL::Exist($sqlStatement)) echo '<br><br><h3 style="color: #1E90FF">Aktuell stehen keine Sets zum Tauschen zur verf&uuml;gung</h3>';
        }

        if($_GET['section']=='hilfe')
        {
            NavBar("Home","Tauschen","Tauschhilfe");

            echo '<h2 style="color: #1E90FF">Wie wird getauscht</h2>';

            echo PageContent(1,CheckEditPermission());
        }

        if($_GET['section']=='uebersicht')
        {
            echo '<h2>Tausch-&Uuml;bersicht</h2>';

            if($_SESSION['userRank']>=99)
            {
                echo '<center>';
                // Verwaltung
                $tradeDataArray = MySQL::Cluster("SELECT * FROM trades ORDER BY dateTradeRequested ASC");
                foreach($tradeDataArray as $tradeData)
                {
                    $partnerData = MySQL::Row("SELECT * FROM users WHERE id = ?",'s',$tradeData['userID']);

                    $capCount = MySQL::Count("SELECT * FROM cart WHERE tradeID = ? AND isSet = '0'",'s',$tradeData['id']);

                    $capCountSets = MySQL::Cluster("SELECT * FROM cart INNER JOIN sets ON cart.objID = sets.id WHERE cart.tradeID = ? AND cart.isSet = '1'",'s',$tradeData['id']);
                    foreach($capCountSets as $countSet) $capCount += $countSet['setSize'];

                    $ownerProfileImage = MySQL::Scalar("SELECT profileImage FROM users WHERE rank = '99'");
                    $traderProfileImage = MySQL::Scalar("SELECT profileImage FROM users WHERE id = ?",'s',$tradeData['userID']);

                    echo '
                        <table class="tradeStatusTable">
                            <tr>
                                <td><div><img src="/files/users/'.($ownerProfileImage == "" ? 'default.png' : $ownerProfileImage).'" alt="" /></div></td>
                                <td><div><img src="/files/users/'.($traderProfileImage == "" ? 'default.png' : $traderProfileImage).'" alt="" /></div></td>
                                <td>
                                    Tauschpartner: '.$partnerData['firstName'].' '.$partnerData['lastName'].' / '.$partnerData['username'].'<br>
                                    <br>
                                    Kronkorken: '.$capCount.'<br>
                                    <br>
                                    Anfrage gestellt: '.str_replace('ä','&auml;',strftime("%d. %B %Y",strtotime($tradeData['dateTradeRequested']))).'<br>
                                    Anfrage best&auml;tigt: '.($tradeData['tradeConfirmed'] ? str_replace('ä','&auml;',strftime("%d. %B %Y",strtotime($tradeData['dateTradeConfirmed']))) : '<i>nicht best&auml;tigt</i>' ).'<br>
                                    Tausch abgeschlossen: '.($tradeData['tradeCompleted'] ? str_replace('ä','&auml;',strftime("%d. %B %Y",strtotime($tradeData['dateTradeCompleted']))) : '<i>nicht abgeschlossen</i>' ).'
                                </td>
                                <td>
                                    <a href="/chat/'.$tradeData['userID'].'/'.$tradeData['id'].'"><button type="button" class="cel_100" style="margin-bottom: 6px;">Nachricht senden</button></a><br>
                                    <a href="#capList'.$tradeData['id'].'"><button type="button" class="cel_100" style="margin-bottom: 6px;">KK-Liste anzeigen</button></a><br>

                                    ';

                                    if($tradeData['tradeCompleted']) echo '<a href="#reactivateTrade'.$tradeData['id'].'"><button type="submit" class="cel_100" style="background: #FF8C00">Tausch reaktivieren</button></a>';
                                    else if($tradeData['tradeConfirmed']) echo '<a href="#completeTrade'.$tradeData['id'].'"><button type="submit" class="cel_100" style="background: #32CD32">Tausch abschlie&szlig;en</button></a>';
                                    else echo '<a href="#confirmTrade'.$tradeData['id'].'"><button type="button" class="cel_100" style="background: #1E90FF">Tausch best&auml;tigen<br>( Starten )</button></a>';

                                    echo '
                                  </td>
                            </tr>
                        </table>

                        ';

                        $tradeInfoTable = '
                            <table class="tradeInfoCapTable">
                                <tr>
                                    <td>Pos.</td>
                                    <td>Bild</td>
                                    <td>Typ</td>
                                    <td>Land</td>
                                    <td>Brauerei</td>
                                    <td>Name</td>
                                    <td>Kapsel-Nummer</td>
                                    <td>St&uuml;ckzahl</td>
                                    <td>Qual.</td>
                                </tr>
                        ';

                        $i=1;
                        $tradeTableData = MySQL::Cluster("SELECT * FROM cart WHERE userID = ? AND tradeID = ?",'ss',$tradeData['userID'],$tradeData['id']);
                        foreach($tradeTableData AS $tradeElement)
                        {
                            if($tradeElement['isSet'])
                            {
                                $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN sets ON bottlecaps.setID = sets.id INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE sets.id = ? AND bottlecaps.id = sets.thumbnailTradeID",'s',$tradeElement['objID']);

                                $tradeInfoTable .= '
                                    <tr>
                                        <td style="text-align: center;">'.$i++.'</td>
                                        <td style="text-align: center;"><img src="/files/sets/'.$capData['countryShort'].'/'.$capData['setFilepath'].'/'.($capData['capImageTrade']=="" ? $capData['capImage'] : $capData['capImageTrade']).'" alt="" /></td>
                                        <td>Set</td>
                                        <td>'.$capData['countryShort'].' - '.$capData['countryDE'].'</td>
                                        <td>'.$capData['breweryName'].'</td>
                                        <td>'.$capData['setName'].'</td>
                                        <td style="text-align: center;"><a href="/tauschen/sets/'.$capData['countryShort'].'/'.$capData['setFilepath'].'">Set anzeigen</a></td>
                                        <td style="text-align: center;">'.$capData['setSize'].' Stk.</td>
                                        <td style="text-align: center;">'.($capData['quality'] == "" ? 'n/a' : $capData['quality']).'</td>
                                    </tr>
                                ';
                            }
                            else
                            {
                                $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.id = ?",'s',$tradeElement['objID']);
                                $setData = MySQL::Row("SELECT * FROM sets WHERE id = ?",'s',$capData['setID']);

                                if($capData['isSet'])
                                {
                                    $capImage = '/files/sets/'.$capData['countryShort'].'/'.$setData['setFilepath'].'/'.($capData['capImageTrade']=="" ? $capData['capImage'] : $capData['capImageTrade']);
                                    $capName = $setData['setName'].' - '.$capData['name'];
                                }
                                else
                                {
                                    $capImage = '/files/bottlecaps/'.$capData['countryShort'].'/'.$capData['breweryFilepath'].'/'.($capData['capImageTrade']=="" ? $capData['capImage'] : $capData['capImageTrade']);
                                    $capName = $capData['name'];
                                }

                                $tradeInfoTable .= '
                                    <tr>
                                        <td style="text-align: center;">'.$i++.'</td>
                                        <td style="text-align: center;"><img src="'.$capImage.'" alt="" /></td>
                                        <td>Kronkorken</td>
                                        <td>'.$capData['countryShort'].' - '.$capData['countryDE'].'</td>
                                        <td>'.$capData['breweryName'].'</td>
                                        <td>'.$capName.'</td>
                                        <td style="text-align: center;"><a href="/suche?suchwert='.$capData['capNumber'].'">'.$capData['capNumber'].'</a></td>
                                        <td style="text-align: center;">-</td>
                                        <td style="text-align: center;">'.($capData['quality'] == "" ? 'n/a' : $capData['quality']).'</td>
                                    </tr>
                                ';
                            }
                        }
                        $tradeInfoTable .= '</table><br><br>';

                        echo '

                        <div class="modal_wrapper" id="capList'.$tradeData['id'].'">
                            <a href="#c"><div class="modal_bg"></div></a>
                            <div class="modal_container" style="width: 50%; height: 70%; background: #F5F5F5; border-radius: 20px;">
                                <h3>Von Partner ausgew&auml;hlte Kronkorken</h3>
                                <div class="sideSignButtons">
                                '.$tradeInfoTable.'
                                </div>
                                <center><a href="#"><button type="button" class="cel_m" style="background: #D60000">Schlie&szlig;en</button></a></center>
                            </div>
                        </div>
                    ';

                    if($tradeData['tradeCompleted'])
                    {
                        echo '
                            <div class="modal_wrapper" id="reactivateTrade'.$tradeData['id'].'">
                                <a href="#c"><div class="modal_bg"></div></a>
                                <div class="modal_container" style="width: 50%; height: 40%; background: #F5F5F5; border-radius: 20px;">
                                    <h3 style="color: #FF8C00">Tausch reaktivieren</h3>
                                    <center>
                                        <form action="/tauschen/uebersicht" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                            Nachricht an den Tauschpartner:
                                            <textarea name="message" style="width: 100%; height: 200px; border-radius: 10px; resize: vertical; padding: 5px;" placeholder="Nachricht an den Tauschpartner..." required>Ein Problem mit dem Tausch ist aufgetreten: </textarea>
                                            <button type="submit" name="reactivateTrade" value="'.$tradeData['id'].'" class="cel_m" style="background: #32CD32">Tausch abschlie&szlig;en</button>
                                            <a href="#"><button type="button" class="cel_m" style="background: #D60000">Abbrechen</button></a>
                                        </form>
                                    </center>
                                </div>
                            </div>
                        ';
                    }
                    else if($tradeData['tradeConfirmed'])
                    {
                        echo '
                            <div class="modal_wrapper" id="completeTrade'.$tradeData['id'].'">
                                <a href="#c"><div class="modal_bg"></div></a>
                                <div class="modal_container" style="width: 50%; height: 40%; background: #F5F5F5; border-radius: 20px;">
                                    <h3 style="color: #32CD32">Tausch abschliessen</h3>
                                    <center>
                                        <form action="/tauschen/uebersicht" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                            Nachricht an den Tauschpartner:
                                            <textarea name="message" style="width: 100%; height: 200px; border-radius: 10px; resize: vertical; padding: 5px;" placeholder="Nachricht an den Tauschpartner..." required>Vielen Dank f&uuml;r den Problemlosen Tausch!
Gerne immer wieder!</textarea>
                                            <button type="submit" name="completeTrade" value="'.$tradeData['id'].'" class="cel_m" style="background: #32CD32">Tausch abschlie&szlig;en</button>
                                            <a href="#"><button type="button" class="cel_m" style="background: #D60000">Abbrechen</button></a>
                                        </form>
                                    </center>
                                </div>
                            </div>
                        ';
                    }
                    else
                    {
                        echo '
                            <div class="modal_wrapper" id="confirmTrade'.$tradeData['id'].'">
                                <a href="#c"><div class="modal_bg"></div></a>
                                <div class="modal_container" style="width: 50%; height: 40%; background: #F5F5F5; border-radius: 20px;">
                                    <h3 style="color: #1E90FF">Tausch best&auml;tigen</h3>
                                    <center>
                                        <form action="/tauschen/uebersicht" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                            Nachricht an den Tauschpartner:
                                            <textarea name="message" style="width: 100%; height: 200px; border-radius: 10px; resize: vertical; padding: 5px;" placeholder="Nachricht an den Tauschpartner..." required></textarea>
                                            <button type="submit" name="confirmTrade" value="'.$tradeData['id'].'" class="cel_m" style="background: #32CD32">Tausch best&auml;tigen</button>
                                            <a href="#"><button type="button" class="cel_m" style="background: #D60000">Abbrechen</button></a>
                                        </form>
                                    </center>
                                </div>
                            </div>
                        ';
                    }
                }

                echo '</center>';
            }
            else
            {
                // Nutzerübersicht

                echo '<center>';
                $tradeDataArray = MySQL::Cluster("SELECT * FROM trades WHERE userID = ? ORDER BY dateTradeRequested ASC",'s',$_SESSION['userID']);
                foreach($tradeDataArray as $tradeData)
                {
                    $partnerData = MySQL::Row("SELECT * FROM users WHERE rank = 99");

                    $capCount = MySQL::Count("SELECT * FROM cart WHERE tradeID = ? AND isSet = '0'",'s',$tradeData['id']);

                    $capCountSets = MySQL::Cluster("SELECT * FROM cart INNER JOIN sets ON cart.objID = sets.id WHERE cart.tradeID = ? AND cart.isSet = '1'",'s',$tradeData['id']);
                    foreach($capCountSets as $countSet) $capCount += $countSet['setSize'];

                    $ownerProfileImage = MySQL::Scalar("SELECT profileImage FROM users WHERE rank = '99'");
                    $traderProfileImage = MySQL::Scalar("SELECT profileImage FROM users WHERE id = ?",'s',$tradeData['userID']);

                    echo '
                        <table class="tradeStatusTable">
                            <tr>
                                <td><div><img src="/files/users/'.($ownerProfileImage == "" ? 'default.png' : $ownerProfileImage).'" alt="" /></div></td>
                                <td><div><img src="/files/users/'.($traderProfileImage == "" ? 'default.png' : $traderProfileImage).'" alt="" /></div></td>
                                <td>
                                    Tauschpartner: '.$partnerData['firstName'].' '.$partnerData['lastName'].' / '.$partnerData['username'].'<br>
                                    <br>
                                    Kronkorken: '.$capCount.'<br>
                                    <br>
                                    Anfrage gestellt: '.str_replace('ä','&auml;',strftime("%d. %B %Y",strtotime($tradeData['dateTradeRequested']))).'<br>
                                    Anfrage best&auml;tigt: '.($tradeData['tradeConfirmed'] ? str_replace('ä','&auml;',strftime("%d. %B %Y",strtotime($tradeData['dateTradeConfirmed']))) : '<i>nicht best&auml;tigt</i>' ).'<br>
                                    Tausch abgeschlossen: '.($tradeData['tradeCompleted'] ? str_replace('ä','&auml;',strftime("%d. %B %Y",strtotime($tradeData['dateTradeCompleted']))) : '<i>nicht abgeschlossen</i>' ).'
                                </td>
                                <td>
                                    <a href="/chat/'.$partnerData['id'].'/'.$tradeData['id'].'"><button type="button" class="cel_100" style="margin-bottom: 6px;">Nachricht senden</button></a><br>
                                    <a href="#capList'.$tradeData['id'].'"><button type="button" class="cel_100" style="margin-bottom: 6px;">KK-Liste anzeigen</button></a><br>
                                </td>
                            </tr>
                        </table>

                        ';

                        $tradeInfoTable = '
                            <table class="tradeInfoCapTable">
                                <tr>
                                    <td>Pos.</td>
                                    <td>Bild</td>
                                    <td>Typ</td>
                                    <td>Land</td>
                                    <td>Brauerei</td>
                                    <td>Name</td>
                                    <td>Kapsel-Nummer</td>
                                    <td>St&uuml;ckzahl</td>
                                    <td>Qual.</td>
                                </tr>
                        ';

                        $i=1;
                        $tradeTableData = MySQL::Cluster("SELECT * FROM cart WHERE userID = ? AND tradeID = ?",'ss',$_SESSION['userID'],$tradeData['id']);
                        foreach($tradeTableData AS $tradeElement)
                        {
                            if($tradeElement['isSet'])
                            {
                                $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN sets ON bottlecaps.setID = sets.id INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE sets.id = ? AND bottlecaps.id = sets.thumbnailTradeID",'s',$tradeElement['objID']);

                                $tradeInfoTable .= '
                                    <tr>
                                        <td style="text-align: center;">'.$i++.'</td>
                                        <td style="text-align: center;"><img src="/files/sets/'.$capData['countryShort'].'/'.$capData['setFilepath'].'/'.($capData['capImageTrade']=="" ? $capData['capImage'] : $capData['capImageTrade']).'" alt="" /></td>
                                        <td>Set</td>
                                        <td>'.$capData['countryShort'].' - '.$capData['countryDE'].'</td>
                                        <td>'.$capData['breweryName'].'</td>
                                        <td>'.$capData['setName'].'</td>
                                        <td style="text-align: center;"><a href="/tauschen/sets/'.$capData['countryShort'].'/'.$capData['setFilepath'].'">Set anzeigen</a></td>
                                        <td style="text-align: center;">'.$capData['setSize'].' Stk.</td>
                                        <td style="text-align: center;">'.($capData['quality'] == "" ? 'n/a' : $capData['quality']).'</td>
                                    </tr>
                                ';
                            }
                            else
                            {
                                $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.id = ?",'s',$tradeElement['objID']);
                                $setData = MySQL::Row("SELECT * FROM sets WHERE id = ?",'s',$capData['setID']);

                                if($capData['isSet'])
                                {
                                    $capImage = '/files/sets/'.$capData['countryShort'].'/'.$setData['setFilepath'].'/'.($capData['capImageTrade']=="" ? $capData['capImage'] : $capData['capImageTrade']);
                                    $capName = $setData['setName'].' - '.$capData['name'];
                                }
                                else
                                {
                                    $capImage = '/files/bottlecaps/'.$capData['countryShort'].'/'.$capData['breweryFilepath'].'/'.($capData['capImageTrade']=="" ? $capData['capImage'] : $capData['capImageTrade']);
                                    $capName = $capData['name'];
                                }

                                $tradeInfoTable .= '
                                    <tr>
                                        <td style="text-align: center;">'.$i++.'</td>
                                        <td style="text-align: center;"><img src="'.$capImage.'" alt="" /></td>
                                        <td>Kronkorken</td>
                                        <td>'.$capData['countryShort'].' - '.$capData['countryDE'].'</td>
                                        <td>'.$capData['breweryName'].'</td>
                                        <td>'.$capName.'</td>
                                        <td style="text-align: center;"><a href="/suche?suchwert='.$capData['capNumber'].'">'.$capData['capNumber'].'</a></td>
                                        <td style="text-align: center;">-</td>
                                        <td style="text-align: center;">'.($capData['quality'] == "" ? 'n/a' : $capData['quality']).'</td>
                                    </tr>
                                ';
                            }
                        }
                        $tradeInfoTable .= '</table><br><br>';

                    echo '
                        <div class="modal_wrapper" id="capList'.$tradeData['id'].'">
                            <a href="#c"><div class="modal_bg"></div></a>
                            <div class="modal_container" style="width: 50%; height: 70%; background: #F5F5F5; border-radius: 20px;">
                                <h3>Ihre ausgew&auml;hlten Kronkorken</h3>
                                <div class="sideSignButtons">
                                '.$tradeInfoTable.'
                                </div>
                                <center><a href="#"><button type="button" class="cel_m" style="background: #D60000">Schlie&szlig;en</button></a></center>
                            </div>
                        </div>
                    ';


                }
                echo '</center>';
            }
        }
    }

	include("_footer.php");
?>
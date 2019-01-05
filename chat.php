<?php
    require("_header.php");

    NavBar("Home","Tauschen","Chat");   

    if(isset($_POST['sendMessage']))
    {
        $receiverID = $_GET['receiverID'];
        $senderID = $_SESSION['userID'];
        $message = $_POST['chatMessage'];
        $tradeID = $_GET['tradeID'];

        Message($senderID, $receiverID, $message, $tradeID);

        $tradeData = MySQL::Row("SELECT * FROM trades WHERE id = ?",'s',$tradeID);
        $userData = MySQL::Row("SELECT * FROM users WHERE id = ?",'s',$tradeData['userID']);

        if($tradeData['userID'] != $_SESSION['userID'] AND $tradeData['mailNotifications'] == '1')
        {
            // ^executed by owner
            // Message to trader
            $traderMessage = "Sie haben eine neue Nachricht von Kro-Ko-Deal.com erhalten!<br><h3>Nachricht:</h3><br>".nl2br($message).'<br><br>';
            $traderMessage = MailFormater(StringOp::GermanSpecialChars($traderMessage),'Neue Nachricht von Kro-Ko-Deal');
            SendEMail($userData['email'],'Neue Nachricht von Kro-Ko-Deal',$traderMessage);
        }

        if($tradeData['userID'] == $_SESSION['userID'])
        {
            // ^executed by trader
            // Message to Owner
            $ownerMessage = "Der Nutzer ".$userData['firstName']." ".$userData['lastName']." / ".$userData['username']."  (".$userData['email'].") hat soeben eine Nachricht gesendet:<br><h3>Nachricht:</h3><br>".nl2br($message).'<br><br>';
            $ownerMessage = MailFormater(StringOp::GermanSpecialChars($ownerMessage),'Neue Nachricht von Kro-Ko-Deal');
            SendEMail("trade@kro-ko-deal.com",'Neue Nachricht von Kro-Ko-Deal',$ownerMessage);
        }

        Page::Redirect(Page::This());
        die();
    }

    if(isset($_GET['receiverID']) AND isset($_SESSION['userID']))
    {
        $receiverData = MySQL::Row("SELECT * FROM users WHERE id = ?",'s',$_GET['receiverID']);
        $senderData = MySQL::Row("SELECT * FROM users WHERE id = ?",'s',$_SESSION['userID']);

        echo '<h3>Konversation mit '.$receiverData['username'].'</h3>';

        echo '
            <br>
            <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <center>
                    <div class="chatContainer">
                        <div class="chatWindow">
                        ';

                        $chatData = MySQL::Cluster("SELECT * FROM messages WHERE (senderID = ? AND receiverID = ?) OR (senderID = ? AND receiverID = ?) ORDER BY date, time ASC LIMIT 0,50",'ssss',$_SESSION['userID'],$_GET['receiverID'],$_GET['receiverID'],$_SESSION['userID']);

                        $lastDate = "";
                        foreach($chatData AS $chatElement)
                        {
                            if($chatElement['date'] != $lastDate)
                            {
                                $lastDate = $chatElement['date'];
                                echo '
                                    <div class="dateDisplay">'.str_replace('ä','&auml;',strftime("%d. %B %Y",strtotime($chatElement['date']))).'</div>
                                    <div class="breaker"></div>
                                ';
                            }

                            if($chatElement['type']=='info')
                            {
                                echo '
                                    <div class="infoDisplay">'.$chatElement['message'].'</div>
                                    <div class="breaker"></div>
                                ';
                            }

                            if($chatElement['type']=='chat')
                            {
                                if($chatElement['senderID'] == $_SESSION['userID'])
                                {
                                    echo '
                                        <div class="sended">
                                            '.FroalaContent($chatElement['message']).'
                                            <span class="timeDisplay">'.date_format(date_create($chatElement['time']),'H:i').'</span>
                                        </div>
                                        <div class="breaker"></div>
                                    ';
                                }
                                else
                                {
                                    echo '
                                        <div class="received">
                                            '.FroalaContent($chatElement['message']).'
                                            <span class="timeDisplay">'.date_format(date_create($chatElement['time']),'H:i').'</span>
                                        </div>
                                        <div class="breaker"></div>
                                    ';
                                }
                            }
                        }

                        echo '
                        </div>
                        <br>
                        <div style="text-align: left;">
                        '.TextareaPlus("chatMessage","chatMessage","",true,true).'
                        </div>

                        <button type="submit" name="sendMessage" style="background: #FF8C00; color: #FFFFFF; margin: 5px;">Senden</button>
                    </div>
                </center>
            </form>
        ';
    }

    include("_footer.php");
?>
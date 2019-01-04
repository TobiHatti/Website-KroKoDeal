<?php
    require("_header.php");

    if(isset($_POST['sendMessage']))
    {
        $receiverID = $_GET['receiverID'];
        $senderID = $_SESSION['userID'];
        $message = $_POST['chatMessage'];
        $tradeID = "";

        Message($senderID, $receiverID, $message, $tradeID);

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
                                    <div class="infoDisplay">Ihr Tausch vom 04.02.2019 wurde best&auml;tigt</div>
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
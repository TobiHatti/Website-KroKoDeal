<?php
	require("_header.php");

    NavBar("Home","G&auml;stebuch"); 

    if(isset($_POST['sendGuestbookEntry']))
    {
        $date = date("Y-m-d");
        MySQL::NonQuery("INSERT INTO guestbook (id,userID,date,message) VALUES ('',?,?,?)",'sss',$_SESSION['userID'],$date,$_POST['message']);
        Page::Redirect(Page::This());
        die();
    }

    if(isset($_POST['upvote']))
    {
        if(MySQL::Exist("SELECT * FROM votes WHERE page = 'gb' AND postID = ? AND userID = ?",'ss',$_POST['upvote'],$_SESSION['userID']))
        MySQL::NonQuery("UPDATE votes SET vote = 'up' WHERE page = 'gb' AND postID = ? AND userID = ?",'ss',$_POST['upvote'],$_SESSION['userID']);
        else MySQL::NonQuery("INSERT INTO votes (id,page,postID,userID,vote) VALUES ('','gb',?,?,'up')",'ss',$_POST['upvote'],$_SESSION['userID']);
    }

    if(isset($_POST['downvote']))
    {
        if(MySQL::Exist("SELECT * FROM votes WHERE page = 'gb' AND postID = ? AND userID = ?",'ss',$_POST['downvote'],$_SESSION['userID']))
        MySQL::NonQuery("UPDATE votes SET vote = 'down' WHERE page = 'gb' AND postID = ? AND userID = ?",'ss',$_POST['downvote'],$_SESSION['userID']);
        else MySQL::NonQuery("INSERT INTO votes (id,page,postID,userID,vote) VALUES ('','gb',?,?,'down')",'ss',$_POST['downvote'],$_SESSION['userID']);
    }

    if(isset($_POST['removeVote']))
    {
        MySQL::NonQuery("DELETE FROM votes WHERE postID = ? AND userID = ?",'ss',$_POST['removeVote'],$_SESSION['userID']);
    }

    echo '<h2>G&auml;stebuch</h2><br>';


    $pager = new Pager(5);
    $pagerOffset = $pager->GetOffset();
    $pagerSize = $pager->GetPagerSize();

    $guestbookArray = MySQL::Cluster("SELECT *,guestbook.id AS postID FROM guestbook INNER JOIN users ON guestbook.userID = users.id INNER JOIN countries ON users.countryID = countries.id ORDER BY date DESC LIMIT ?,?",'ss',$pagerOffset,$pagerSize);

    echo '<center>';

    if(isset($_SESSION['userID']))
    {
        echo '
            <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div style="text-align: left;">
                '.TextareaPlus("message","message","",true,true).'
                </div>
                <button type="submit" name="sendGuestbookEntry" class="cel_m" style="margin: 5px;">Posten</button>
                <br><br>
            </form>
        ';
    }

    echo $pager->SQLAuto("SELECT *,guestbook.id AS postID FROM guestbook INNER JOIN users ON guestbook.userID = users.id INNER JOIN countries ON users.countryID = countries.id ORDER BY date DESC");

    echo '<form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">';
    foreach($guestbookArray AS $gbEntry)
    {
        $upVotes = MySQL::Count("SELECT * FROM votes WHERE page = 'gb' AND vote = 'up' AND postID = ?",'s',$gbEntry['postID']);
        $downVotes = MySQL::Count("SELECT * FROM votes WHERE page = 'gb' AND vote = 'down' AND postID = ?",'s',$gbEntry['postID']);

        $isUpVoted = false;
        $isDownVoted = false;
        if(isset($_SESSION['userID']))
        {
            $isUpVoted = MySQL::Exist("SELECT * FROM votes WHERE page = 'gb' AND vote = 'up' AND postID = ? AND userID = ?",'ss',$gbEntry['postID'],$_SESSION['userID']);
            $isDownVoted = MySQL::Exist("SELECT * FROM votes WHERE page = 'gb' AND vote = 'down' AND postID = ? AND userID = ?",'ss',$gbEntry['postID'],$_SESSION['userID']);
        }

        echo '
            <table class="guestbookEntry">
                <tr>
                    <td>
                        <img class="userImg" src="/files/users/'.$gbEntry['profileImage'].'" alt="" />
                        <i>Eintrag von:</i><br>
                        <b>'.$gbEntry['username'].'</b><br>
                        ';

                        switch($gbEntry['rank'])
                        {
                            case 1: echo '<em>[<span style="color: #1E90FF">Nutzer</span>]</em>'; break;
                            case 1: echo '<em>[<span style="color: #00FFFF">Tauschpartner</span>]</em>'; break;
                            case 95: echo '<em>[<span style="color: #8B008B">Moderator</span>]</em>'; break;
                            case 98: echo '<em>[<span style="color: #FF0000">Entwickler</span>]</em>'; break;
                            case 99: echo '<em>[<span style="color: #FFA500">Inhaber</span>]</em>'; break;
                            default: break;
                        }

                        echo '<br><br><br><br>';

                        if($isUpVoted) echo '<button type="submit" name="removeVote" value="'.$gbEntry['postID'].'" class="up"><img src="/content/upvoted.png" alt="" />'.$upVotes.'</button>';
                        else echo '<button type="submit" name="upvote" value="'.$gbEntry['postID'].'" class="up"><img src="/content/upvote.png" alt="" />'.$upVotes.'</button>';

                        if($isDownVoted)echo '<button type="submit" name="removeVote" value="'.$gbEntry['postID'].'" class="down"><img src="/content/downvoted.png" alt="" />'.$downVotes.'</button>';
                        else echo '<button type="submit" name="downvote" value="'.$gbEntry['postID'].'" class="down"><img src="/content/downvote.png" alt="" />'.$downVotes.'</button>';

                        echo '
                    </td>
                    <td>
                        ';

                        if(isset($_SESSION['userID']) AND $_SESSION['userID'] == $gbEntry['userID']) echo '<a href="/entfernen/gaestebuch/'.$gbEntry['postID'].'"><u>L&ouml;schen</u></a>';

                        echo '
                        <i>Gepostet am '.str_replace('ä','&auml;',strftime("%d. %B %Y",strtotime($gbEntry['date']))).'</i>
                        <br>
                        '.$gbEntry['message'].'
                    </td>
                </tr>
            </table>
        ';
    }
    echo '</form>';

    echo $pager->SQLAuto("SELECT *,guestbook.id AS postID FROM guestbook INNER JOIN users ON guestbook.userID = users.id INNER JOIN countries ON users.countryID = countries.id ORDER BY date DESC");

    echo '</center>';

	include("_footer.php");
?>
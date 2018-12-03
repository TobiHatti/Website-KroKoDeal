<?php
    require("_headerincludes.php");


    echo '
        <html id="iframePage">
            <head>
    ';

    require("_headerlinks.php");

    echo '
            </head>
            <body id="iframeBody" style="margin: 0;">
                <center>
    ';

    $offset = $_GET['offset'];


    echo BottlecapSingleBox(MySQL::Scalar("SELECT * FROM bottlecaps WHERE isCounted = 1 AND isSet = 0 ORDER BY id DESC LIMIT ?,1",'i',$offset));

    echo '<a href="?offset='.($offset+1).'" class="navigationLink" style="text-decoration:none;">&#9664; Zur&uuml;ck &#10074;</a>';

    echo '&nbsp;&nbsp;';

    if($offset==0) echo '<span style="color: #696969">&#10074; Weiter &#9654;</span>';
    else echo '<a href="?offset='.($offset-1).'" class="navigationLink" style="text-decoration:none;">&#10074; Weiter &#9654;</a>';

    echo '
                </center>
            </body>
        </html>
    ';


?>
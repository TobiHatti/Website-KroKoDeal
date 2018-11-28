<?php
    require("_headerincludes.php");


    echo '
        <html id="iframefoundation">
            <head>
    ';

    require("_headerlinks.php");

    echo '
            </head>
            <body>
    ';

    $offset = 0;

    $strSQL = "SELECT * FROM bottlecaps WHERE isOwned = 1 AND isSet = 0 ORDER BY id DESC LIMIT $offset,1";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs)) echo BottlecapSingleBox($row['id']);

    echo '
            </body>
        </html>
    ';


?>
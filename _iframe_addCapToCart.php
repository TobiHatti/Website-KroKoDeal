<?php
    session_start();
    setlocale (LC_ALL, 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de', 'ge', 'de_DE.ISO_8859-1', 'German_Germany');         
    require("_headerincludes.php");
    require("_headerlinks.php");

    if(isset($_GET['objID']))
    {
        if(!isset($_SESSION['userID']))
        {
            echo '<script>window.parent.location.replace("/sign-in");</script>';
            die();
        }

        MySQL::NonQuery("INSERT INTO cart (id,userID,objID,isSet) VALUES (NULL,?,?,?)",'sss',$_SESSION['userID'],$_GET['objID'],$_GET['isSet']);

        echo '
            <script>
                    var cartCount = '.MySQL::Count("SELECT id FROM cart WHERE userID = ? AND tradeID = ''",'s',$_SESSION['userID']).'

                    window.parent.document.getElementById("outCartCount1").innerHTML = "(" + cartCount + ")";
                    window.parent.document.getElementById("outCartCount2").innerHTML = "(" + cartCount + ")";

                    window.parent.document.getElementById("cartAddNotification").style.display = "block";
                    setTimeout(function() {
                        window.parent.document.getElementById("cartAddNotification").style.display = "none";
                        window.location.replace("/_iframe_addCapToCart.php");
                    }, 3500);
            </script>
        ';
    }



?>
<?php
    session_start();
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
                    window.parent.document.getElementById("cartAddNotification").style.display = "block";
                    setTimeout(function() {
                        window.parent.document.getElementById("cartAddNotification").style.display = "none";
                        window.location.replace("/_iframe_addCapToCart.php");
                    }, 3500);
            </script>
        ';
    }



?>
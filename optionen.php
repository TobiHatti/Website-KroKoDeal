<?php
    require("_header.php");

    if(isset($_GET['option']))
    {
        if($_GET['option']=='vorschau')
        {
            $capID = $_GET['objID1'];
            $setID = $_GET['objID2'];

            MySQL::NonQuery("UPDATE sets SET thumbnailID = ? WHERE id = ?",'ss',$capID,$setID);

            echo '
                <script>
                    window.history.back();
                </script>
            ';
            die();
        }

        if($_GET['option']=='tauschvorschau')
        {
            $capID = $_GET['objID1'];
            $setID = $_GET['objID2'];

            MySQL::NonQuery("UPDATE sets SET thumbnailTradeID = ? WHERE id = ?",'ss',$capID,$setID);

            echo '
                <script>
                    window.history.back();
                </script>
            ';
            die();
        }
    }

    include("_footer.php");
?>
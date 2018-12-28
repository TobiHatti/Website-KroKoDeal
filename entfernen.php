<?php
    require("_header.php");

    if(CheckEditPermission())
    {
        if(isset($_POST['delete']))
        {
            $id = $_GET['selectionID'];

            if($_GET['section']=='kronkorken')
            {
                if(MySQL::Scalar("SELECT isSet FROM bottlecaps WHERE id = ?",'s',$id))
                {
                    $setID = MySQL::Scalar("SELECT setID FROM bottlecaps WHERE id = ?",'s',$id);
                    $setSize = MySQL::Scalar("SELECT setSize FROM sets WHERE id = ?",'s',$setID);
                    $setSize -= 1;
                    MySQL::NonQuery("UPDATE sets SET setSize = ? WHERE id = ?",'ss',$setSize,$setID);
                }

                MySQL::NonQuery("DELETE FROM bottlecaps WHERE id = ?",'s',$id);
            }

            if($_GET['section']=='set')
            {
                MySQL::NonQuery("DELETE FROM bottlecaps WHERE setID = ?",'s',$id);
                MySQL::NonQuery("DELETE FROM sets WHERE id = ?",'s',$id);
            }

            echo '
                <script>
                    window.history.back();
                    window.history.back();
                </script>
            ';
            die();
        }

        if(isset($_POST['abort']))
        {
            echo '
                <script>
                    window.history.back();
                    window.history.back();
                </script>
            ';
        }

        echo '
            <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <center>
                    <br>
                    <h1 class="stagfade1" style="color: #CC0000">Eintrag wirklich l&ouml;schen?</h1>
                    <br>
                    <h2 class="stagfade2">Nach dem l&ouml;schen kann der Eintrag nicht wiederhergestellt werden!</h2>
                    <br><br>
                    <button type="submit" name="delete" class="cef_warning">L&ouml;schen</button>
                    <button type="submit" name="abort">Abbrechen</button>
                </center>
            </form>
        ';
    }


    include("_footer.php");
?>
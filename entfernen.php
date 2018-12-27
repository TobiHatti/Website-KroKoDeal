<?php
    require("_header.php");

    if(CheckEditPermission())
    {
        if(isset($_POST['delete']))
        {
            $tableCode = $_GET['section'];

            if($_GET['section']=='kronkorken')

            switch($_GET['section'])
            {
                case 'kronkorken': $table = 'bottlecaps'; break;

                default: $table = 'NOTABLESELECTED'; break;
            }

            $id = $_GET['selectionID'];

            MySQL::NonQuery("DELETE FROM $table WHERE id = ?",'@s',$id);
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
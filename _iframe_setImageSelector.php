<?php
    setlocale (LC_ALL, 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de', 'ge', 'de_DE.ISO_8859-1', 'German_Germany');
    require("_headerincludes.php");

    echo '
        <html style="background:transparent">
            <head>
    ';
    require("_headerlinks.php");
    echo '
            </head>
            <body>
                <a href="'.Page::This().'"><button type="button" style="border: 2px solid #32CD32"><i class="fa fa-refresh" aria-hidden="true"></i> Aktualisieren</button></a>
                <div class="setCapSelector">
                    <center>
                ';

                $directory = 'files/sets/'.$_GET['country'].'/'.$_GET['set'].'/';
                $scanned_directory = array_diff(scandir($directory), array('..', '.'));

                foreach($scanned_directory as $file)
                {
                    echo '<a href="/eintragen/set-konfigurieren?set='.$_GET['set'].'#" target="_top" onclick="SelectCapImageSet(\'/'.$directory.$file.'\')"><img src="/'.$directory.$file.'"/></a>';
                }

                echo '
                    </center>
                </div>
            </body>
        </html>
    ';

?>
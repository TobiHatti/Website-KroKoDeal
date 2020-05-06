<?php
    setlocale (LC_ALL, 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de', 'ge', 'de_DE.ISO_8859-1', 'German_Germany');         
    require("_headerincludes.php");

    if(isset($_POST['uploadSetImg']))
    {
        $setPath = $_GET['set'];
        $countryShort = $_GET['country'];

        $fileUploader = new FileUploader();
        $fileUploader->SetFileElement("uploadSetImages");
        $fileUploader->SetName("tmpSetUpload{#u}");
        $fileUploader->SetPath("files/sets/$countryShort/$setPath");
        $fileUploader->OverrideDuplicates(false);
        $fileUploader->Upload();

        Page::Redirect(Page::This("-added","+added"));
        die();
    }

    echo '
        <html style="background:transparent">
            <head>
    ';
    require("_headerlinks.php");
    echo '
            </head>
            <body>
                <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <center>
                        '.(isset($_GET['added']) ? '<span style="color: #32CD32">Dateien hochgeladen!</span><br>' : '').'

                        Weitere Fotos hinzuf&uuml;gen (max. 20)
                        <br>
                        '.FileButton("uploadSetImages","uploadSetImages",true).'
                        <br><br>
                        <button type="submit" name="uploadSetImg">Hochladen</button>
                    </center>
                </form>
            </body>
        </html>
    ';

?>
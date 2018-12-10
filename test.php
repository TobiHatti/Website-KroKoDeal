<?php
	include("_header.php");


    if(isset($_POST['subm']))
    {
        $fileUploader = new FileUploader();

        $fileUploader->SetFileElement("sampleUpload");
        $fileUploader->SetName("test");
        $fileUploader->SetPath("testImg/");
        $fileUploader->SetFileTypes("png","jpg");
        $fileUploader->SetMaxFileSize("106KB");
        //$fileUploader->SetTargetAspectRatio("10:1");
        //$fileUploader->SetTargetResolution(300,350);
        $fileUploader->SetScaleFactor(2);
        $fileUploader->OverrideDuplicates(false);
        $fileUploader->Upload();

    }


    echo '<br><br><br><br><form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">';
    echo FileButton("sampleUpload","sampleUpload");
    echo '<br><button type="submit" name="subm">Send</button></form>';



	include("_footer.php");
?>
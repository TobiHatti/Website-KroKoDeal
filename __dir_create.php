<?php
   require("_header.php");


   function cryptoload()
    {
        include("data/mysql_connect.php");
        $strSQL = "SELECT * FROM settings WHERE rule LIKE 'encrypt_keys'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $_SESSION['crypto1']=$row['value0'];
            $_SESSION['crypto2']=$row['value1'];
            $_SESSION['crypto3']=$row['value2'];
        }
    }

    function id_decrypt($id)
    {
        return (($_SESSION['crypto2']*$_SESSION['crypto3']+$id)/$_SESSION['crypto1'])/$_SESSION['crypto3'];
    }

    function id_encrypt($id)
    {
        return ($id*$_SESSION['crypto1']-$_SESSION['crypto2'])*$_SESSION['crypto3'];
    }

    function SReplace($string)
    {
        // DESCRIPTION:
        // Formats a given string so it is save for URL-names etc.
        // $string  The string that should be formated

        // Replacing "Ä,ä,Ö,ö,Ü,ü,ß" and "-" (HTML-Characters)
        $sstr = str_replace(' ','-',$string);
        $sstr = str_replace('&Auml;','AE',$sstr);
        $sstr = str_replace('&auml;','ae',$sstr);
        $sstr = str_replace('&Ouml;','OE',$sstr);
        $sstr = str_replace('&ouml;','oe',$sstr);
        $sstr = str_replace('&Uuml;','UE',$sstr);
        $sstr = str_replace('&uuml;','ue',$sstr);
        $sstr = str_replace('&szlig;','ss',$sstr);

        // Replacing "Ä,ä,Ö,ö,Ü,ü,ß" (UTF-Characters/Database)
        $sstr = str_replace('Ã„','AE',$sstr);
        $sstr = str_replace('Ã¤','ae',$sstr);
        $sstr = str_replace('Ã–','OE',$sstr);
        $sstr = str_replace('Ã¶','oe',$sstr);
        $sstr = str_replace('Ãœ','UE',$sstr);
        $sstr = str_replace('Ã¼','ue',$sstr);
        $sstr = str_replace('ÃŸ','ss',$sstr);

        // Remove everything but Alphanumeric letters and numbers and "-"
        $sstr = preg_replace('/[^0-9A-Za-z-.+_\|]/', '', $sstr);

        return $sstr;
    }

    $linkOld = mysqli_connect("localhost","root","","krokodeal_old");
    cryptoload();

    //Brewery Transfer
    /*
    $strSQL = "SELECT *,breweries.name AS brewName FROM caps INNER JOIN breweries ON caps.brewery_id = breweries.id GROUP BY breweries.id";
    $rs=mysqli_query($linkOld,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $id = $row['id'];
        $countryID = $row['country_id'];
        $regionID = $row['federal_id'];
        $breweryName = $row['brewName'];
        $breweryShort = explode('_',$row['cap_nr'])[1];
        $breweryLink = $row['link'];
        $breweryImage = SReplace($row['file']);
        $breweryFilepath = SReplace($row['brewName']);

        $sqlStatement ="INSERT INTO breweries (id,countryID,regionID,breweryName,breweryShort,breweryLink,breweryImage,breweryFilepath) VALUES ('$id','$countryID','$regionID','$breweryName','$breweryShort','$breweryLink','$breweryImage','$breweryFilepath')";

        //echo $sqlStatement.'<br>';

        MySQL::NonQuery($sqlStatement);
    }
    */

    /*
    // Set Transfer
    $strSQL = "SELECT * FROM sets";
    $rs=mysqli_query($linkOld,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $id = $row['id'];
        $thumbnailID = $row['thumbnail_id'];
        $setSize = $row['set_size'];
        $setName = $row['set_name'];
        $setFilepath = SReplace($row['set_name']);

        $sqlStatement ="INSERT INTO sets (id,thumbnailID,setSize,setName,setFilepath) VALUES ('$id','$thumbnailID','$setSize','$setName','$setFilepath')";

        //echo $sqlStatement.'<br>';

        MySQL::NonQuery($sqlStatement);
    }
    */

    /*
    // Erzeugen Länder-Verzeichnisse
    $strSQL = "SELECT * FROM countries";
    $rs=mysqli_query($linkOld,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        mkdir("files/bottlecaps/".$row['short'],0750);
        mkdir("files/breweries/".$row['short'],0750);
        mkdir("files/sets/".$row['short'],0750);
    }
    */

    /*
    // Umsiedlung Brauerei-Bilder
    $strSQL = "SELECT * FROM breweries INNER JOIN countries ON breweries.country_id = countries.id";
    $rs=mysqli_query($linkOld,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $oldFilepath = 'files/old/breweries/'.$row['short'].'/'.$row['file'];
        $newFilepath = 'files/breweries/'.$row['short'].'/'.SReplace($row['file']);
        $newDir = 'files/breweries/'.$row['short'].'/';

        //echo 'OLD: '.$oldFilepath.'<br>';
        //echo 'NEW: '.$newFilepath.'<br><br>';

        mkdir($newDir,0750);
        rename($oldFilepath,$newFilepath);
    }
    */

    /*
    // Umsiedlung KK-Bilder (!= SET)
    $strSQL = "SELECT *,breweries.name AS brewName, breweries.id AS brewID FROM caps INNER JOIN countries ON countries.id = caps.country_id INNER JOIN breweries ON caps.brewery_id = breweries.id WHERE set_id = 0";
    $rs=mysqli_query($linkOld,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $oldFilepath = 'files/old/bottlecaps/'.$row['short'].'/'.id_encrypt($row['brewID'])  .'/'.$row['cap_nr'].'.JPG';
        $newFilepath = 'files/bottlecaps/'.$row['short'].'/'.SReplace($row['brewName']).'/'.$row['cap_nr'].'.JPG';
        $newDir = 'files/bottlecaps/'.$row['short'].'/'.SReplace($row['brewName']).'/';

        //echo 'OLD: '.$oldFilepath.'<br>';
        //echo 'NEW: '.$newFilepath.'<br><br>';

        mkdir($newDir,0750);
        rename($oldFilepath,$newFilepath);
    }
    */


    // Umsiedlung KK-Bilder (== SET)
    $strSQL = "SELECT *,breweries.name AS brewName, breweries.id AS brewID FROM caps INNER JOIN countries ON countries.id = caps.country_id INNER JOIN breweries ON caps.brewery_id = breweries.id INNER JOIN sets ON caps.set_id = sets.id WHERE caps.set_id != '0'";
    $rs=mysqli_query($linkOld,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $oldFilepath = 'files/old/bottlecaps/_sets/'.$row['short'].'/'.$row['set_id'].'/'.$row['cap_nr'].'.JPG';
        $newFilepath = 'files/sets/'.$row['short'].'/'.SReplace($row['set_name']).'/'.$row['cap_nr'].'.JPG';
        $newDir = 'files/sets/'.$row['short'].'/'.SReplace($row['set_name']).'/';

        //echo 'OLD: '.$oldFilepath.'<br>';
        //echo 'NEW: '.$newFilepath.'<br><br>';

        mkdir($newDir,0750);
        rename($oldFilepath,$newFilepath);
    }



    /*
    $strSQL = "SELECT *, breweries.name AS bname FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        if($row['isSet'])
        {

        }
        else
        {
            $oldPath = 'files/old_bottlecaps/'.$row['short'].'/'.id_encrypt($row['breweryID']).'/'.$row['capNumber'].'.JPG';

            $newPath = 'files/bottlecaps/'.$row['short'].'/'.SReplace($row['bname']).'/'.$row['capNumber'].'.JPG';
            $newDir = 'files/bottlecaps/'.$row['short'].'/'.SReplace($row['bname']).'/';
        }

        mkdir($newDir,0750);
        rename($oldPath,$newPath);

        //echo $oldPath.'<br>';
        //echo $newPath.'<br>';


        /*
        if($row['isSet'])
        {

        }
        else
        {
            echo $oldPath = 'files/old_bottlecaps/'.$row['short'].'/'.id_encrypt($row['breweryID']).'/'.$row['capNumber'].'.JPG';

            $newPath = 'files/bottlecaps/'.$row['short'].'/'.SReplace($row['bname']).'/'.$row['capNumber'].'.JPG';
            $newDir = 'files/bottlecaps/'.$row['short'].'/'.SReplace($row['bname']).'/';
        }


        //mkdir($newDir,0750);
        //rename($oldPath,$newPath);

        //echo $oldPath.'<br>';
        //echo $newPath.'<br>';

    }
    */


    /*

    $strSQL = "SELECT * FROM breweries INNER JOIN countries ON breweries.countryID = countries.id";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {

        $oldPath = 'files/old_breweries/breweries/'.$row['short'].'/'.$row['image'];

        $newPath = 'files/breweries/'.$row['short'].'/'.SReplace($row['image']);

        rename($oldPath,$newPath);

        echo $oldPath.'<br>';
        echo $newPath.'<br>';

    }

    */

    /*
    $strSQL = "SELECT *, sets.name AS setName FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id INNER JOIN sets ON bottlecaps.setID = sets.id";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        if($row['isSet'])
        {
            $oldPath = 'files/old_sets/'.$row['short'].'/'.$row['setID'].'/'.$row['capNumber'].'.JPG';

            $newPath = 'files/sets/'.$row['short'].'/'.SReplace($row['setName']).'/'.$row['capNumber'].'.JPG';
            $newDir = 'files/sets/'.$row['short'].'/'.SReplace($row['setName']).'/';
        }

        mkdir($newDir,0750);
        rename($oldPath,$newPath);

        echo $oldPath.'<br>';
        echo $newPath.'<br>';

    } */

    /*
    $strSQL = "SELECT * FROM sets";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        MySQL::NonQuery("UPDATE sets SET filepath = ? WHERE id = ?","@s",SReplace($row['name']),$row['id']);
    }
    */

    /*
    $strSQL = "SELECT * FROM sidesigns";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $id = $row['id'];
        $newFile = $row['name'].'.'.$row['sidesignImage'];

        MySQL::NonQuery("UPDATE sidesigns SET sidesignImage = '$newFile' WHERE id = '$id'");
    }
    */

    /*
    $strSQL = "SELECT * FROM countries INNER JOIN country_list ON countries.countryShort = country_list.alpha3";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        //MySQL::NonQuery("UPDATE countries SET countryShort2 = ? WHERE countryShort = ?",'@s',$row['alpha2'],$row['alpha3']);
        MySQL::NonQuery("UPDATE countries SET countryShort2 = ? WHERE countryEN = ?",'@s',$row['alpha2'],$row['name']);
    }

    */

    /*
    $strSQL = "SELECT * FROM bottlecaps WHERE isSet = '0'";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $short = explode('_',$row['capNumber']);
        echo 'Kurz: '.$short[1].' ('.$row['capNumber'].')<br>';

        MySQL::NonQuery("UPDATE breweries SET breweryShort = ? WHERE id = ?",'@s',$short[1],$row['breweryID']);
    }
    */

    /*

    $strSQL = "SELECT * FROM breweries";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs)){
        $id = $row['id'];
        $newImage = SReplace($row['breweryImage'],'.');

        MySQL::NonQuery("UPDATE breweries SET breweryImage = ? WHERE id = ?",'@s',$newImage,$id);
    }

    */
?>
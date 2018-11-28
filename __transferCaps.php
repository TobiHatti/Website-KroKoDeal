<?php
    require("_header.php");

    $strSQL = "SELECT * FROM caps";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $country_id = $row['country_id'];
        $federal_id = $row['federal_id'];
        $kind_id = $row['kind_id'];
        $brewery_id = $row['brewery_id'];
        $sidesign_id = $row['sidesign_id'];
        $base_color_id = $row['base_color_id'];
        $cap_color_id = $row['cap_color_id'];
        $text_color_id = $row['text_color_id'];
        $set_id = $row['set_id'];
        $name = $row['name'];
        $get_location = $row['get_location'];
        $get_year = $row['get_year'];
        $stock = $row['stock'];
        $add_date = $row['add_date'];
        $cap_nr = $row['cap_nr'];
        $traded = $row['traded'];
        $cap_cond = $row['cap_cond'];
        $twist = $row['twist'];
        $tradeable = $row['tradeable'];
        $info = $row['info'];
        $in_collection = $row['in_collection'];
        $display_in_collection = $row['display_in_collection'];
        $quality = $row['quality'];
        $image = $cap_nr.'.JPG';

        $quality = ($quality==0) ? '' : $quality;

        $isSet = ($set_id==0) ? 0 : 1;
        $isUsed = ($cap_cond==0) ? 1 : 0;

        $addedDateParts = explode('.',$add_date);

        $addedDate = $addedDateParts[2].'-'.$addedDateParts[1].'-'.$addedDateParts[0];

        $sqlInsert = "
        INSERT INTO
        `bottlecaps`
        (`id`, `name`, `info`, `capNumber`, `countryID`, `federalID`, `flavorID`, `breweryID`, `sidesignID`, `baseColorID`, `capColorID`, `textColorID`, `setID`, `isSet`, `isTraded`, `isUsed`, `isTwitslock`, `isTradeable`, `isOwned`, `isDisplayed`, `locationAquired`, `dateAquired`, `dateInserted`, `quality`, `stock`, `image`, `showSideImage`, `sideImage`)
        VALUES
        ('', '$name', '$info', '$cap_nr', '$country_id', '$federal_id', '$kind_id', '$brewery_id', '$sidesign_id', '$base_color_id', '$cap_color_id', '$text_color_id', '$set_id', '$isSet', '$traded', '$isUsed', '$twist', '$tradeable', '$in_collection', '$display_in_collection', '$get_location', '$get_year', '$addedDate', '$quality', '$stock', '$image', '0', '');";


        MySQL::NonQuery($sqlInsert);
    }

?>
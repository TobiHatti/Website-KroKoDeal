<?php
    require("_header.php");


    if(!isset($_GET['set']))
    {
        if(!isset($_GET['country']))
        {
            echo '<h2 style="color: #1E90FF">Sets</h2>';

            echo '<center>';
            $buttonArray = MySQL::Cluster("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE isSet = '1' GROUP BY countries.countryShort");
            foreach($buttonArray AS $button) echo CountryButton($button['countryShort'],false,true,true);
            echo '</center>';
        }
        else
        {
            $country = $_GET['country'];
            $countryData = MySQL::Row("SELECT * FROM countries WHERE countryShort = ?",'s',$country);

            $permissionCheck = CheckEditPermission();

            echo '<h2 style="color: #1E90FF">Sets aus '.$countryData['countryDE'].'</h2>';

            echo '<center>';
            $setCluster = MySQL::Cluster("SELECT * FROM sets INNER JOIN bottlecaps ON sets.id = bottlecaps.setID INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE countries.countryShort = ? GROUP BY sets.id",'s',$_GET['country']);
            foreach($setCluster AS $setData)
            {
                $allTradeable = true;

                $capCluster = MySQL::Cluster("SELECT * FROM bottlecaps WHERE setID = ?",'s',$setData['setID']);
                foreach($capCluster AS $capData)
                {
                    if($capData['isTradeable'] != '1') $allTradeable = false;
                }

                if($allTradeable)
                {
                    echo SetTile($setData['setID'],$permissionCheck,true);
                }
            }

            echo '</center>';

        }
    }


    else
    {
        $pager = new Pager(20);
        $pagerOffset = $pager->GetOffset();
        $pagerSize = $pager->GetPagerSize();
        $pager->SetColorSet(2);

        $setFilepath = $_GET['set'];
        $setData = MySQL::Row("SELECT * FROM sets INNER JOIN bottlecaps ON sets.id = bottlecaps.setID INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE sets.setFilepath = ?",'s',$setFilepath);

        if(MySQL::Exist("SELECT regions.id FROM regions INNER JOIN countries ON regions.countryID = countries.id WHERE countries.countryShort = ?",'s',$setData['countryShort'])) $countryHasRegions = true;
        else $countryHasRegions = false;

        $sqlStatement = "SELECT *,
        bottlecaps.id AS bottlecapID,
        capColor.colorShort AS bottlecapCapColorShort,
        capColor.colorDE AS bottlecapCapColorName,
        baseColor.hex AS bottlecapBaseColorValue,
        baseColor.colorDE AS bottlecapBaseColorName,
        textColor.hex AS bottlecapTextColorValue,
        textColor.colorDE AS bottlecapTextColorName
        FROM bottlecaps
        INNER JOIN breweries ON bottlecaps.breweryID = breweries.id
        INNER JOIN countries ON breweries.countryID = countries.id
        ".($countryHasRegions ? "INNER JOIN regions ON breweries.regionID = regions.id" : "")."
        INNER JOIN flavors ON bottlecaps.flavorID = flavors.id
        INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id
        INNER JOIN sets ON bottlecaps.setID = sets.id
        INNER JOIN colors AS capColor ON bottlecaps.capColorID = capColor.id
        INNER JOIN colors AS baseColor ON bottlecaps.baseColorID = baseColor.id
        INNER JOIN colors AS textColor ON bottlecaps.textColorID = textColor.id
        WHERE countries.countryShort = ?
        AND sets.setFilepath = ?
        ORDER BY breweries.breweryName, bottlecaps.capNumber ASC
        LIMIT $pagerOffset,$pagerSize";

        $capDataArray = MySQL::Cluster($sqlStatement,'@s',$setData['countryShort'],$setFilepath);
        $sqlPager = $pager->SQLAuto(str_replace(" LIMIT $pagerOffset,$pagerSize","",$sqlStatement),'@s',$setData['countryShort'],$setFilepath);

        echo '<h2 style="color: #1E90FF">'.$setData['setName'].'</h2>';


        $capThumbnailArray = MySQL::Cluster("SELECT * FROM bottlecaps WHERE setID = ?",'i',$setData['setID']);
        echo '<center><div class="setCapThumbnailContainer">';
        for($i=0;$i<count($capThumbnailArray);$i++)
        {
            if($i < $pager->GetPagerSize() * 10) $page = 10;
            if($i < $pager->GetPagerSize() * 9) $page = 9;
            if($i < $pager->GetPagerSize() * 8) $page = 8;
            if($i < $pager->GetPagerSize() * 7) $page = 7;
            if($i < $pager->GetPagerSize() * 6) $page = 6;
            if($i < $pager->GetPagerSize() * 5) $page = 5;
            if($i < $pager->GetPagerSize() * 4) $page = 4;
            if($i < $pager->GetPagerSize() * 3) $page = 3;
            if($i < $pager->GetPagerSize() * 2) $page = 2;
            if($i < $pager->GetPagerSize() * 1) $page = 1;

            echo '<a href="?page='.$page.'#cap'.$capThumbnailArray[$i]['id'].'"><img src="/files/sets/'.$setData['countryShort'].'/'.$setData['setFilepath'].'/'.$capThumbnailArray[$i]['capImage'].'"/></a>';
        }
        echo '</div>';

        echo '
                <br>
                '.$sqlPager.'
                <div class="bottlecapRowContainer">
                    <table class="capDisplay">
                        <tr>
                            <td colspan=5>Set "'.$setData['setName'].'" von '.$setData['breweryName'].'</td>
                        </tr>
        ';
        $permissionCheck = CheckEditPermission();

        foreach($capDataArray AS $capData)  echo BottleCapRowData($capData, true, $countryHasRegions, $permissionCheck,true);

        echo '</table><div class="infoOverlays">';

        foreach($capDataArray AS $capData) echo BottleCapRowInfoOverlay($capData,$permissionCheck);

        echo '</div></div><br>'.$sqlPager.'</center>';


    }

    echo '<iframe src="/_iframe_addCapToCart" name="cartAddFrame" frameborder="0" hidden></iframe>';

    include("_footer.php");
?>
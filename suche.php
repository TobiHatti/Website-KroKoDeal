<?php
	require("_header.php");

    if(isset($_POST['searchValue']) AND $_POST['searchValue']!="") Page::Redirect(Page::This("+suchwert=".$_POST['searchValue']));
    else if(isset($_GET['suchwert']))
    {
        $searchValue = '%'.$_GET['suchwert'].'%';
        echo '<h2>Suchergebnisse f&uuml;r "'.$_GET['suchwert'].'"</h2>';

        $pager = new Pager(20);
        $pagerOffset = $pager->GetOffset();
        $pagerSize = $pager->GetPagerSize();

        $countryHasRegions = false;

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
        INNER JOIN flavors ON bottlecaps.flavorID = flavors.id
        INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id
        INNER JOIN colors AS capColor ON bottlecaps.capColorID = capColor.id
        INNER JOIN colors AS baseColor ON bottlecaps.baseColorID = baseColor.id
        INNER JOIN colors AS textColor ON bottlecaps.textColorID = textColor.id
        WHERE
        bottlecaps.name LIKE ? OR
        bottlecaps.capNumber LIKE ? OR
        breweries.breweryName LIKE ? OR
        flavors.flavorDE LIKE ? OR
        countries.countryDE LIKE ?
        ORDER BY breweries.breweryName, bottlecaps.capNumber ASC
        LIMIT $pagerOffset,$pagerSize
        ";

        $capDataArray = MySQL::Cluster($sqlStatement,'@s',$searchValue,$searchValue,$searchValue,$searchValue,$searchValue);
        $sqlPager = $pager->SQLAuto(str_replace(" LIMIT $pagerOffset,$pagerSize","",$sqlStatement),'@s',$searchValue,$searchValue,$searchValue,$searchValue,$searchValue);

        echo '
            <center>
                <br>
                '.$sqlPager.'
                <div class="bottlecapRowContainer">
                    <table class="capDisplay">
                        <tr>
                            <td colspan=5>Suchergebnisse</td>
                        </tr>
        ';

        foreach($capDataArray AS $capData) echo BottleCapRowData($capData, false, $countryHasRegions);

        echo '</table><div class="infoOverlays">';

        foreach($capDataArray AS $capData) echo BottleCapRowInfoOverlay($capData);

        echo '</div></div><br>'.$sqlPager.'</center>';

    }
    else echo '<h2>Kein Suchwert angegeben!</h2>';

	include("_footer.php");
?>
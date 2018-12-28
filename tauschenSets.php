<?php
    require("_header.php");


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
            echo  $setData['setName'];
        }
    }




    include("_footer.php");
?>
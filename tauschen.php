<?php
	require("_header.php");

    if(isset($_GET['section']))
    {
        if($_GET['section']=='laender')
        {
            echo '<h2 style="color: #1E90FF">Kronkorken Tauschen</h2><br>';

            echo '<center>';

            $rows = MySQL::Cluster("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE isTradeable = '1' GROUP BY countries.countryShort");
            foreach($rows AS $row) echo TradeCountryButton($row['countryShort'],true,false,true);
            
            echo '</center>';
        }

        if($_GET['section']=='sets')
        {
            echo '<h2 style="color: #1E90FF">Sets Tauschen</h2>';

            echo '<center>';
            $buttonArray = MySQL::Cluster("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE isSet = '1' AND isTradeable = '1' GROUP BY countries.countryShort");
            foreach($buttonArray AS $button) echo TradeCountryButton($button['countryShort'],false,true,true);
            echo '</center>';
        }
    }



	include("_footer.php");
?>
<?php
	require("_header.php");

    NavBar("Home");


    echo '

        <h1>Wilkommen bei <span style="color: #7FFF00">KRO - KO - DEAL</span> !</h1>
        <h2>Dein Kronkorken-Dealer</h2>

        <br>
        <div class="homeNewsAndStats">
            <div>
                <h3><u>Neueste Meldungen</u></h3>

                <b>Wilkommen bei Kro-Ko-Deal! </b><br>
                <br>
                Von nun an können sie alle<br>
                exklusiven vorteile für angemeldete<br>
                Nutzer genießen und können<br>
                Tausche mit den Inhaber tätigen!<br>
                <br>
                Mit freundlichen grüßen,<br>
                Peter Hattinger
            </div>

            <div>
                <h3><u>Statistik</u></h3>

                <table>
                    <tr>
                        <td>Aktueller Kronkorken-Stand:<br><br></td>
                        <td>'.MySQL::Count("SELECT id FROM bottlecaps WHERE (isSet = 0 AND isCounted = 1) OR (isSet = 1 AND isOwned = 1)").' Stück<br><br></td>
                    </tr>
                    <tr>
                        <td>Heute hinzugefügt:</td>
                        <td>'.MySQL::Count("SELECT id FROM bottlecaps WHERE dateInserted LIKE '".date("Y-m-d")."'").' Stück</td>
                    </tr>
                    <tr>
                        <td>Diesen Monat hinzugefügt:</td>
                        <td>'.MySQL::Count("SELECT id FROM bottlecaps WHERE dateInserted LIKE '".date("Y-m-")."%'").' Stück</td>
                    </tr>
                    <tr>
                        <td>Dieses Jahr hinzugefügt:</td>
                        <td>'.MySQL::Count("SELECT id FROM bottlecaps WHERE dateInserted LIKE '".date("Y-")."%'").' Stück</td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="wowslider-container1">
        	<div class="ws_images">
        	    <ul>
                ';

                $sliderRows = MySQL::Cluster("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.isSet = '0' ORDER BY bottlecaps.id DESC LIMIT 0,6");
                $sliderMessages = array("KRO-KO-DEAL","EST. 2016","Dein Kronkorken Dealer","KRO-KO-DEAL","EST. 2016","Dein Kronkorken Dealer");
                $i=1;
                foreach($sliderRows AS $slide)
                {
                    $altMessage = '';
                    $imagePath = '/files/bottlecaps/'.$slide['countryShort'].'/'.$slide['breweryFilepath'].'/'.$slide['capImage'].'" alt="Kro-Ko-Deal" title="'.$sliderMessages[$i-1];
                    if(!file_exists(ltrim($imagePath,'/')))
                    {
                        $altMessage = $imagePath;
                        $imagePath = '/content/not_found.png';
                    }

                    echo '<li><img src="'.$imagePath.'" id="wows1_'.$i++.'" width="350px" height="350px" style="border-radius:25px; alt="'.$altMessage.'"/></li>';
                }

                echo '
            	</ul>
            </div>
        	<div class="ws_bullets">
        	    <div>
                ';

                $i=1;
                foreach($sliderRows AS $slide)
                {
                    $altMessage = '';
                    $imagePath = '/files/bottlecaps/'.$slide['countryShort'].'/'.$slide['breweryFilepath'].'/'.$slide['capImage'].'" alt="Kro-Ko-Deal" title="'.$sliderMessages[$i-1];
                    if(!file_exists(ltrim($imagePath,'/')))
                    {
                        $altMessage = $imagePath;
                        $imagePath = '/content/not_found.png';
                    }

                    echo '<a href="#" title=""><span><img src="'.$imagePath.'" alt="Kro-Ko-Deal" class="thumbnail"/>'.$i++.'</span></a>';
                }

                echo '
        	    </div>
            </div>
            <div class="ws_script" style="position:absolute;left:-99%"></div>
        	<div class="ws_shadow"></div>
        </div>
        <script type="text/javascript" src="/plugins/wowslider/wowslider.js"></script>
        <script type="text/javascript" src="/plugins/wowslider/script.js"></script>

        <br><br>

        <h3><u>Neueste Kronkorken</u></h3>
        <iframe src="_iframe_neuesteKronkorken.php?offset=0" frameborder="0" scrolling="no" style="width: 100%; height: 235px;" ></iframe>

        <br>
        <h3><u>Sammlung</u></h3>

        <center>
    ';

    $rows = MySQL::Cluster("SELECT * FROM continents");
    foreach($rows AS $row) echo ContinentButton($row['continentShort'],true,true);

    echo '
        </center>
    ';

	include("_footer.php");
?>
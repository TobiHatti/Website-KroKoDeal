<?php
	require("_header.php");

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
                <h3><u>Statistiken</u></h3>

                <table>
                    <tr>
                        <td>Aktueller Kronkorken-Stand:<br><br></td>
                        <td>1000 Stück<br><br></td>
                    </tr>
                    <tr>
                        <td>Heute hinzugefügt:</td>
                        <td>1000 Stück</td>
                    </tr>
                    <tr>
                        <td>Diesen Monat hinzugefügt:</td>
                        <td>1000 Stück</td>
                    </tr>
                    <tr>
                        <td>Dieses Jahr hinzugefügt:</td>
                        <td>1000 Stück</td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="wowslider-container1">
        	<div class="ws_images">
        	    <ul>
                    <li><img src="#" alt="Kro-Ko-Deal" title="Message" id="wows1_1" width="350px" height="350px" style="border-radius:25px; "/></li>
                    <li><img src="#" alt="Kro-Ko-Deal" title="Message" id="wows1_2" width="350px" height="350px" style="border-radius:25px; "/></li>
                    <li><img src="#" alt="Kro-Ko-Deal" title="Message" id="wows1_3" width="350px" height="350px" style="border-radius:25px; "/></li>
            	</ul>
            </div>
        	<div class="ws_bullets">
        	    <div>
                    <a href="#" title="Kro-Ko-Deal"><span><img src="#" alt="Kro-Ko-Deal" class="thumbnail"/>1</span></a>
                    <a href="#" title="Kro-Ko-Deal"><span><img src="#" alt="Kro-Ko-Deal" class="thumbnail"/>2</span></a>
                    <a href="#" title="Kro-Ko-Deal"><span><img src="#" alt="Kro-Ko-Deal" class="thumbnail"/>3</span></a>
        	    </div>
            </div>
            <div class="ws_script" style="position:absolute;left:-99%"></div>
        	<div class="ws_shadow"></div>
        </div>
        <script type="text/javascript" src="/plugins/wowslider/wowslider.js"></script>
        <script type="text/javascript" src="/plugins/wowslider/script.js"></script>

        <br><br>

        <h3><u>Neueste Kronkorken</u></h3>
        <iframe src="_iframe_neuesteKronkorken.php" frameborder="1" scrolling="no" style="width: 100%;" ></iframe>

    ';


	include("_footer.php");
?>
<?php
	require("_header.php");

//########################################################################################
//########################################################################################
//      POST PART
//########################################################################################
//########################################################################################

    if(isset($_POST['addBottlecap']))
    {
        $breweryID = $_POST['breweryID'];
        $name = $_POST['name'];
        $flavorID = $_POST['flavorID'];
        $capNumber = $_POST['capNumber'];

        $locationAquired = $_POST['locationAquired'];
        $dateAquired = $_POST['dateAquired'];
        $quality = $_POST['quality'];
        $isTraded = isset($_POST['isTraded']) ? 1 : 0;
        $isTradeable = isset($_POST['isTradeable']) ? 1 : 0;
        $alcohol = ($_POST['alcohol']=="") ? null : $_POST['alcohol'];
        $stock = ($_POST['stock']=="") ? 0 : $_POST['stock'];

        $capColorID = explode('-',$_POST['capColorID'])[0];
        $baseColorID = explode('-',$_POST['baseColorID'])[0];
        $textColorID = explode('-',$_POST['textColorID'])[0];
        $isUsed = isset($_POST['isUsed']) ? 1 : 0;
        $isTwistLock = isset($_POST['isTwistLock']) ? 1 : 0;
        $isCounted = isset($_POST['isCounted']) ? 1 : 0;

        $sidesignID = $_POST['sidesignID'];
        $dateInserted = date("Y-m-d");

        $sqlStatement = "
        INSERT INTO bottlecaps
        (id, name, capNumber, flavorID, breweryID, sidesignID, baseColorID, capColorID, textColorID, isTraded, isUsed, isTwistlock, isTradeable, isCounted, locationAquired, dateAquired, dateInserted, quality, alcohol, stock)
        VALUES
        (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

        MySQL::NonQuery($sqlStatement,'@s',$name,$capNumber,$flavorID,$breweryID,$sidesignID,$baseColorID,$capColorID,$textColorID,$isTraded,$isUsed,$isTwistLock,$isTradeable,$isCounted,$locationAquired,$dateAquired,$dateInserted,$quality,$alcohol,$stock);

        $capID = MySQL::Scalar("SELECT id FROM bottlecaps ORDER BY id DESC");
        $countryShort = MySQL::Scalar("SELECT countryShort FROM countries INNER JOIN breweries ON countries.id = breweries.countryID WHERE breweries.id = ?",'i',$breweryID);
        $breweryFilepath = MySQL::Scalar("SELECT breweryFilepath FROM breweries WHERE id = ?",'i',$breweryID);

        $fileUploader = new FileUploader();
        $fileUploader->SetFileElement("capImage");
        $fileUploader->SetTargetResolution(500,500);
        $fileUploader->SetPath("files/bottlecaps/$countryShort/$breweryFilepath/");
        $fileUploader->SetSQLEntry("UPDATE bottlecaps SET capImage = '@FILENAME' WHERE id = '$capID'");
        $fileUploader->SetName($capNumber);
        $fileUploader->OverrideDuplicates(false);
        $fileUploader->Upload();

        Page::Redirect(Page::This());
        die();
    }

    if(isset($_POST['addBrewery']))
    {
        $countryID = $_POST['countryID'];
        $regionID = isset($_POST['regionID']) ? $_POST['regionID'] : 0;
        $link = $_POST['link'];
        $name = $_POST['name'];
        $short = $_POST['breweryShort'];
        $saveName = StringOp::SReplace($name);

        $sqlStatement = "INSERT INTO breweries
        (id,countryID,regionID,breweryName,breweryShort,breweryLink,breweryFilepath)
        VALUES
        (NULL,?,?,?,?,?,?)";

        MySQL::NonQuery($sqlStatement,'@s',$countryID,$regionID,$name,$short,$link,$saveName);
        $breweryID = MySQL::Scalar("SELECT id FROM breweries ORDER BY id DESC");
        $countryShort = MySQL::Scalar("SELECT countryShort FROM countries INNER JOIN breweries ON countries.id = breweries.countryID WHERE breweries.id = ?",'i',$breweryID);

        $fileUploader = new FileUploader();
        $fileUploader->SetFileElement("breweryImage");
        $fileUploader->SetTargetResolution(1000,1000);
        $fileUploader->SetPath("files/breweries/$countryShort/");
        $fileUploader->SetSQLEntry("UPDATE breweries SET breweryImage = '@FILENAME' WHERE id = '$breweryID'");
        $fileUploader->SetName($saveName);
        $fileUploader->OverrideDuplicates(false);
        $fileUploader->Upload();

        Page::Redirect(Page::This());
        die();
    }

    if(isset($_POST['addFlavor']))
    {
        $flavorDE = $_POST['flavorDE'];
        $flavorEN = $_POST['flavorEN'];
        MySQL::NonQuery("INSERT INTO flavors (id,flavorDE,flavorEN) VALUES (NULL,?)",'s',$flavorDE,$flavorEN);
        Page::Redirect(Page::This());
        die();
    }

    if(isset($_POST['addSidesign']))
    {
        $sidesignName = $_POST['name'];

        $fileUploader = new FileUploader();
        $fileUploader->SetFileElement("sidesignImage");
        $fileUploader->SetPath("files/sidesigns/");
        $fileUploader->SetSQLEntry("INSERT INTO sidesigns (id, sidesignName, sidesignImage) VALUES (NULL,'$sidesignName','@FILENAME')");
        $fileUploader->OverrideDuplicates(false);
        $fileUploader->Upload();
    }

//########################################################################################
//########################################################################################
//      MAIN PAGE
//########################################################################################
//########################################################################################

    if(isset($_GET['section']) AND isset($_SESSION['userID']) AND $_SESSION['userRank'] > 90)
    {

//========================================================================================
//========================================================================================
//      ADD BOTTLECAP
//========================================================================================
//========================================================================================

        if($_GET['section'] == 'kronkorken')
        {
            echo '<h2>Kronkorken hinzuf&uuml;gen</h2>';

            $countryList = MySQL::Cluster("SELECT * FROM countries RIGHT JOIN breweries ON countries.id = breweries.countryID GROUP BY breweries.countryID ORDER BY countries.countryDE ASC");
            $flavorList = MySQL::Cluster("SELECT * FROM flavors");
            $colorList = MySQL::Cluster("SELECT * FROM colors");
            $sidesignFrequentList = MySQL::Cluster("SELECT *,COUNT(sidesignID) AS sidesignCount FROM bottlecaps INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id GROUP BY sidesignID HAVING sidesignCount >= 3");
            $sidesignAllList = MySQL::Cluster("SELECT * FROM sidesigns ORDER BY sidesignName ASC");
            $qualityValueList = array("A" => "A","B" => "B","C" => "C","D" => "D","E" => "E");
            $qualityDisplayList = array("A" => "A - Neu","B" => "B - Benutzt, Sehr guter Zustand","C" => "C - Benutzt, kleine Kratzer/Knicke","D" => "D - Benutzt, gro&szlig;e Kratzer/Knicke","E" => "E - Benutzt, schlechter zust.");

            echo '
                <center>
                    <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">

                        <input type="hidden" id="outBreweryShort"/>
                        <input type="hidden" id="outCountryShort"/>

                        <table class="addCapTable">
                            <tr>
                                <td colspan=3>Allgemeines</td>
                                <td rowspan=14></td>
                                <td colspan=3>Bilder</td>
                            </tr>

                            <tr>
                                <td>Land</td>
                                <td>
                                    <select name="countryID" class="cel_100 cef_nomg cef_nopd" id="countryList" onchange="DynLoadList(1,this,\'--- Ausw\u00e4hlen ---\',\'breweryList\',\'SELECT breweryName AS dynLoadText, id AS dynLoadValue FROM breweries WHERE countryID = ?? ORDER BY breweryName ASC\'); DynLoadScalar(2,this,\'outCountryShort\',\'SELECT countryShort2 FROM countries WHERE id = ??\')">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($countryList AS $country) echo '<option value="'.$country['countryID'].'">'.$country['countryDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveCountryID","saveCountryID","",true).'</td>

                                <td rowspan=5>
                                    <center>
                                        <div class="bottlecapColorSchemeContainerEntry">
                                            <div><img name="capPreviewCapColor" id="GLD" src="/content/capUsedColored.png" alt="" /></div>
                                            <div name="capPreviewBaseColor" style="background: #FFFFFF"></div>
                                            <div name="capPreviewTextColor" style="color: #FF0000">K-K-D</div>
                                            <div name="capPreviewTwistLock"></div>
                                        </div>
                                    </center>
                                </td>
                                <td rowspan=5 colspan=2>
                                    <center>
                                        <img src="" alt="" id="capImagePreview"/><br>
                                        '.FileButton("capImage","capImage",false,"ReadURL(this,'capImagePreview');","","width: 100px; line-height: 5px;",true).'
                                    </center>
                                </td>

                            </tr>

                            <tr>
                                <td>Brauerei</td>
                                <td>
                                    <select name="breweryID" class="cel_m cef_nomg cef_nopd" id="breweryList" required
                                    onchange="DynLoadScalar(3,this,\'outBreweryShort\',\'SELECT breweryShort FROM breweries WHERE id = ??\'); CopyShortsToCapNumber(false);"
                                    onclick="DynLoadScalar(3,this,\'outBreweryShort\',\'SELECT breweryShort FROM breweries WHERE id = ??\'); CopyShortsToCapNumber(false)">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                    </select>
                                </td>
                                <td>'.Tickbox("saveBreweryID","saveBreweryID","",true).'</td>
                            </tr>

                            <tr>
                                <td>Name</td>
                                <td><input class="cel_m" type="text" name="name" placeholder="Name..." required/></td>
                                <td>'.Tickbox("saveName","saveName","",true).'</td>
                            </tr>

                            <tr>
                                <td>Sorte</td>
                                <td>
                                    <select name="flavorID" class="cel_100 cef_nomg cef_nopd" id="" required>
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($flavorList AS $flavor) echo '<option value="'.$flavor['id'].'">'.$flavor['flavorDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveFlavorID","saveFlavorID","",true).'</td>
                            </tr>

                            <tr>
                                <td>Kapsel-Nr</td>
                                <td><input class="cel_m" type="text" name="capNumber" id="capNumber" placeholder="XX_XX_XXXX" required onclick="CopyShortsToCapNumber(true)"/></td>
                                <td>'.Tickbox("saveCapNumber","saveCapNumber","",true).'</td>
                            </tr>

                            <tr>
                                <td colspan=3>Zusatzinfos</td>
                                <td colspan=3>Optische angaben</td>
                            </tr>

                            <tr>
                                <td>Erhaltsort</td>
                                <td><input class="cel_m" type="text" name="locationAquired" placeholder="Erhaltsort..."/></td>
                                <td>'.Tickbox("saveLocationAquired","saveLocationAquired","",true).'</td>

                                <td>Kapselfarbe</td>
                                <td>
                                    <select name="capColorID" class="cel_100 cef_nomg cef_nopd" id="capColor" onchange="InsertCapUpdateCapPreview()">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($colorList AS $color) echo '<option style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'-'.$color['colorShort'].'">'.$color['colorDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveCapColorID","saveCapColorID","",true).'</td>
                            </tr>

                            <tr>
                                <td>Erhaltsdatum</td>
                                <td><input class="cel_m" type="text" name="dateAquired" placeholder="Erhaltsdatum..."/></td>
                                <td>'.Tickbox("saveDateAquired","saveDateAquired","",true).'</td>

                                <td>Grundfarbe</td>
                                <td>
                                    <select name="baseColorID" class="cel_100 cef_nomg cef_nopd" id="baseColor" onchange="InsertCapUpdateCapPreview()">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($colorList AS $color) echo '<option style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'-'.$color['hex'].'">'.$color['colorDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveBaseColorID","saveBaseColorID","",true).'</td>
                            </tr>

                            <tr>
                                <td>Qualit&auml;t</td>
                                <td>
                                    <select name="quality" class="cel_m cef_nomg cef_nopd" id="">
                                        <option value="" selected>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($qualityValueList AS $quality) echo '<option value="'.$quality.'">'.$qualityDisplayList[$quality].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveQuality","saveQuality","",true).'</td>

                                <td>Textfarbe</td>
                                <td>
                                    <select name="textColorID" class="cel_100 cef_nomg cef_nopd" id="textColor" onchange="InsertCapUpdateCapPreview()">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($colorList AS $color) echo '<option style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'-'.$color['hex'].'">'.$color['colorDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveTextColorID","saveTextColorID","",true).'</td>
                            </tr>

                            <tr>
                                <td>Erhalten durch</td>
                                <td>
                                    <table>
                                        <tr>
                                            <td>'.RadioButton("Tausch","isTraded",false,"1").'</td>
                                            <td>'.RadioButton("Kauf","isTraded",true,"0").'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>'.Tickbox("saveIsTraded","saveIsTraded","",true).'</td>

                                <td>Zustand</td>
                                <td>
                                    <table>
                                        <tr>
                                            <td>'.RadioButton("Neu","isUsed",false,"0","InsertCapUpdateCapPreview()").'</td>
                                            <td>'.RadioButton("Gebr.","isUsed",true,"1","InsertCapUpdateCapPreview()").'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>'.Tickbox("saveIsUsed","saveIsUsed","",true).'</td>
                            </tr>

                            <tr>
                                <td>Tauschbar</td>
                                <td>
                                    <table>
                                        <tr>
                                            <td>'.RadioButton("Ja","isTradeable",false,"1").'</td>
                                            <td>'.RadioButton("Nein","isTradeable",true,"0").'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>'.Tickbox("saveIsTradeable","saveIsTradeable","",true).'</td>

                                <td>Drehverschluss</td>
                                <td>
                                    <table>
                                        <tr>
                                            <td>'.RadioButton("Ja","isTwistLock",false,"1","InsertCapUpdateCapPreview()").'</td>
                                            <td>'.RadioButton("Nein","isTwistLock",true,"0","InsertCapUpdateCapPreview()").'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>'.Tickbox("saveIsTwistLock","saveIsTwistLock","",true).'</td>
                            </tr>

                            <tr>
                                <td>Alkoholgehalt</td>
                                <td><input class="cel_m" type="number" step="0.1" name="alcohol" placeholder="Alkoholgehalt..."/></td>
                                <td>'.Tickbox("saveAlcohol","saveAlcohol","",true).'</td>


                            </tr>

                            <tr>
                                <td>Auf Lager</td>
                                <td><input class="cel_m" type="number" step="1" name="stock" placeholder="Auf Lager..."/></td>
                                <td>'.Tickbox("saveAlcohol","saveAlcohol","",true).'</td>

                                <td>Mitz&auml;hlen</td>
                                <td>'.Tickbox("isCounted","isCounted","",true).'</td>
                                <td>'.Tickbox("saveIsCounted","saveIsCounted","",true).'</td>
                            </tr>

                            <tr>
                                <td colspan=7>
                                    <br>
                                    <button type="submit" name="addBottlecap">Kronkorken hinzuf&uuml;gen</button>
                                </td>
                            </tr>
                        </table>


                        <table class="addCapSidesignTable">
                            <tr><td>Randzeichen</td></tr>
                            <tr>
                                <td>
                                ';
                                foreach($sidesignFrequentList AS $sidesign)
                                {
                                    echo '
                                        <input type="radio" id="sidesignFrequent'.$sidesign['sidesignID'].'" value="'.$sidesign['sidesignID'].'" name="sidesignID" hidden required/>
                                        <label for="sidesignFrequent'.$sidesign['sidesignID'].'">
                                            <div>
                                                <img src="/files/sidesigns/'.$sidesign['sidesignImage'].'" alt="" />
                                            </div>
                                        </label>
                                    ';
                                }
                                echo '
                                </td>
                            </tr>
                            <tr><td><a href="#allSidesigns">Alle Randzeichen</a></td></tr>
                        </table>

                        <div class="modal_wrapper" id="allSidesigns">
                            <a href="#c"><div class="modal_bg"></div></a>
                            <div class="modal_container" style="width: 50%; height: 40%; background: #2b2b2b; border-radius: 20px;">
                                <h3>Alle Randzeichen</h3>
                                <div class="sideSignButtons">
                                ';

                                foreach($sidesignAllList AS $sidesign)
                                    {
                                        echo '
                                            <input type="radio" id="sidesignAll'.$sidesign['id'].'" value="'.$sidesign['id'].'" name="sidesignID" hidden required/>
                                            <label for="sidesignAll'.$sidesign['id'].'">
                                                <div>
                                                    <img src="/files/sidesigns/'.$sidesign['sidesignImage'].'" alt="" />
                                                </div>
                                            </label>
                                        ';
                                    }

                                echo '
                                </div>
                            </div>
                        </div>
                    </form>
                </center>
            ';
        }

//========================================================================================
//========================================================================================
//      ADD SET
//========================================================================================
//========================================================================================

        if($_GET['section'] == 'set')
        {
            echo '<h2>Set hinzuf&uuml;gen</h2>';
        }

//========================================================================================
//========================================================================================
//      ADD BREWERY
//========================================================================================
//========================================================================================

        if($_GET['section'] == 'brauerei')
        {
            echo '<h2>Brauerei hinzuf&uuml;gen</h2>';

            echo '
                <input type="hidden" value="0" id="outCountryHasRegions"/>
                <script>
                    setInterval(function() {
                        ToggleElementVisibilityByElement("outCountryHasRegions","tableRowRegions","table-row");
                    }, 100);
                </script>

                <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <center>
                        <table class="addBreweryTable">
                            <tr>
                                <td colspan=3>Brauerei eintragen</td>
                            </tr>
                            <tr>
                                <td>Brauerei-Name: </td>
                                <td><input type="text" name="name" placeholder="Brauerei..." class="cel_m cef_nomg"/></td>
                                <td rowspan=5>
                                    <img src="#" alt="" id="breweryImagePreview"/><br><br>
                                    '.FileButton("breweryImage","breweryImage",false,"ReadURL(this,'breweryImagePreview');","","width: 100px; line-height: 5px;",true).'
                                </td>
                            </tr>
                            <tr>
                                <td>Brauerei-K&uuml;rzel: </td>
                                <td><input type="text" name="breweryShort" placeholder="K&uuml;rzel..." class="cel_m cef_nomg"/></td>
                            </tr>
                            <tr>
                                <td>Land: </td>
                                <td>
                                    <select name="countryID" id="" class="cel_m cef_nomg" onchange="DynLoadExist(this,\'outCountryHasRegions\',\'SELECT * FROM regions WHERE countryID = ??\');">
                                        <option value="" disabled selected>--- Ausw&auml;hlen ---</option>
                                        ';
                                        $countryList = MySQL::Cluster("SELECT * FROM countries ORDER BY countryDE ASC");
                                        foreach($countryList AS $country) echo '<option value="'.$country['id'].'">'.$country['countryDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                            </tr>



                            <tr id="tableRowRegions" style="display:none;">
                                <td>Bundesland: </td>
                                <td>
                                    <select name="regionID" id="" class="cel_m cef_nomg">
                                        <option value="" disabled selected>--- Ausw&auml;hlen ---</option>
                                        ';
                                        $regionList = MySQL::Cluster("SELECT * FROM regions ORDER BY regionDE ASC");
                                        foreach($regionList AS $region) echo '<option value="'.$region['id'].'">'.$region['regionDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Homepage-Link: </td>
                                <td><input type="url" name="link" placeholder="http://..." class="cel_m cef_nomg"/></td>
                            </tr>
                            <tr>
                                <td colspan=3>
                                    <br>
                                    <button type="submit" name="addBrewery">Brauerei hinzuf&uuml;gen</button>
                                </td>
                            </tr>
                        </table>
                    </center>
                </form>
            ';
        }

//========================================================================================
//========================================================================================
//      ADD FLAVOR
//========================================================================================
//========================================================================================

        if($_GET['section'] == 'sorte')
        {
            echo '<h2>Sorte hinzuf&uuml;gen</h2>';

            echo '
                <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <center>
                        <table class="addFlavorTable">
                            <tr>
                                <td colspan=2>Sorte hinzuf&uuml;gen</td>
                            </tr>
                            <tr>
                                <td>Sorte (DE): </td>
                                <td><input type="text" name="flavorDE" placeholder="Sorte Deutsch..." class="cel_m cef_nomg"/></td>
                            </tr>
                            <tr>
                                <td>Sorte (EN): </td>
                                <td><input type="text" name="flavorEN" placeholder="Sorte Englisch..." class="cel_m cef_nomg"/></td>
                            </tr>
                            <tr>
                                <td colspan=3>
                                    <br>
                                    <button type="submit" name="addFlavor">Sorte hinzuf&uuml;gen</button>
                                </td>
                            </tr>
                        </table>
                    </center>
                </form>
            ';
        }

//========================================================================================
//========================================================================================
//      ADD SIDESIGN
//========================================================================================
//========================================================================================

        if($_GET['section'] == 'randzeichen')
        {
            echo '<h2>Randzeichen hinzuf&uuml;gen</h2>';

            echo '
                <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <center>
                        <table class="addFlavorTable">
                            <tr>
                                <td colspan=2>Randzeichen hinzuf&uuml;gen</td>
                            </tr>
                            <tr>
                                <td>Bezeichnung: </td>
                                <td><input type="text" name="name" placeholder="Randzeichen-Bez. ..." class="cel_m cef_nomg"/></td>
                            </tr>
                            <tr>
                                <td>Bild: </td>
                                <td>
                                    <img src="#" alt="" id="SidesignImagePreview"/><br><br>
                                    '.FileButton("sidesignImage","sidesignImage",false,"ReadURL(this,'SidesignImagePreview');","","width: 100px; line-height: 5px;",true).'
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3>
                                    <br>
                                    <button type="submit" name="addSidesign">Randzeichen hinzuf&uuml;gen</button>
                                </td>
                            </tr>
                        </table>
                    </center>
                </form>
            ';
        }
    }
	
	include("_footer.php");
?>
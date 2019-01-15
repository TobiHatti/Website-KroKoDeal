<?php
	require("_header.php");

    NavBar("Home","Kronkorken","Eintragen und bearbeiten");

//########################################################################################
//########################################################################################
//      POST PART
//########################################################################################
//########################################################################################

    if(isset($_POST['addBottlecap']) OR isset($_POST['editBottlecap']) OR isset($_POST['expandSet']))
    {
        $urlKeepExtension = '';

        $breweryID = isset($_POST['breweryID']) ? $_POST['breweryID'] : '';
        $name = $_POST['name'];
        $flavorID = isset($_POST['flavorID']) ? $_POST['flavorID'] : '';
        $capNumber = $_POST['capNumber'];

        $locationAquired = $_POST['locationAquired'];
        $dateAquired = $_POST['dateAquired'];
        $quality = $_POST['quality'];
        $isTraded = $_POST['isTraded'];
        $isTradeable = $_POST['isTradeable'];
        $alcohol = isset($_POST['alcohol']) ? (($_POST['alcohol']=="") ? null : $_POST['alcohol']) : '';
        $stock = ($_POST['stock']=="") ? 0 : $_POST['stock'];

        $capColorID = explode('-',$_POST['capColorID'])[0];
        $baseColorID = explode('-',$_POST['baseColorID'])[0];
        $textColorID = explode('-',$_POST['textColorID'])[0];
        $isUsed = $_POST['isUsed'];
        $isTwistLock = $_POST['isTwistLock'];
        $isCounted = isset($_POST['isCounted']) ? 1 : 0;

        $isOwned = isset($_POST['isOwned']) ? 1 : 0;

        $sidesignID = $_POST['sidesignID'];
        $dateInserted = date("Y-m-d");


        $countryShort = MySQL::Scalar("SELECT countryShort FROM countries INNER JOIN breweries ON countries.id = breweries.countryID WHERE breweries.id = ?",'i',$breweryID);
        $breweryFilepath = MySQL::Scalar("SELECT breweryFilepath FROM breweries WHERE id = ?",'i',$breweryID);

        $fileUploader = new FileUploader();
        $fileUploader->SetFileElement("capImage");
        $fileUploader->SetTargetResolution(500,500);


        if(isset($_POST['addBottlecap']))
        {
            $sqlStatement = "
            INSERT INTO bottlecaps
            (id, name, capNumber, flavorID, breweryID, sidesignID, baseColorID, capColorID, textColorID, isTraded, isUsed, isTwistlock, isTradeable, isCounted, locationAquired, dateAquired, dateInserted, quality, alcohol, stock, isOwned)
            VALUES
            (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,'1');";

            MySQL::NonQuery($sqlStatement,'@s',$name,$capNumber,$flavorID,$breweryID,$sidesignID,$baseColorID,$capColorID,$textColorID,$isTraded,$isUsed,$isTwistLock,$isTradeable,$isCounted,$locationAquired,$dateAquired,$dateInserted,$quality,$alcohol,$stock);

            $capID = MySQL::Scalar("SELECT id FROM bottlecaps ORDER BY id DESC");

            $fileUploader->SetPath("files/bottlecaps/$countryShort/$breweryFilepath/");
            $fileUploader->SetSQLEntry("UPDATE bottlecaps SET capImage = '@FILENAME' WHERE id = '$capID'");

            if(isset($_POST['saveCountryID'])) $urlKeepExtension .= '&countryID='.$_POST['countryID'];
            if(isset($_POST['saveBreweryID'])) $urlKeepExtension .='&breweryID='.$_POST['breweryID'];
            if(isset($_POST['saveName'])) $urlKeepExtension .= '&name='.$_POST['name'];
            if(isset($_POST['saveFlavorID'])) $urlKeepExtension .= '&flavorID='.$_POST['flavorID'];
            if(isset($_POST['saveCapNumber'])) $urlKeepExtension .= '&capNumber='.$_POST['capNumber'];
            if(isset($_POST['saveLocationAquired'])) $urlKeepExtension .= '&locationAquired='.$_POST['locationAquired'];
            if(isset($_POST['saveDateAquired'])) $urlKeepExtension .= '&dateAquired='.$_POST['dateAquired'];
            if(isset($_POST['saveCapColorID'])) $urlKeepExtension .= '&capColorID='.$_POST['capColorID'];
            if(isset($_POST['saveBaseColorID'])) $urlKeepExtension .= '&baseColorID='.$_POST['baseColorID'];
            if(isset($_POST['saveTextColorID'])) $urlKeepExtension .= '&textColorID='.$_POST['textColorID'];
            if(isset($_POST['saveQuality'])) $urlKeepExtension .= '&quality='.$_POST['quality'];
            if(isset($_POST['saveIsTraded'])) $urlKeepExtension .= '&isTraded='.$_POST['isTraded'];
            if(isset($_POST['saveIsUsed'])) $urlKeepExtension .= '&isUsed='.$_POST['isUsed'];
            if(isset($_POST['saveIsTradeable'])) $urlKeepExtension .= '&isTradeable='.$_POST['isTradeable'];
            if(isset($_POST['saveIsTwistLock'])) $urlKeepExtension .= '&isTwistLock='.$_POST['isTwistLock'];
            if(isset($_POST['saveAlcohol'])) $urlKeepExtension .= '&alcohol='.$_POST['alcohol'];
            if(isset($_POST['saveStock'])) $urlKeepExtension .= '&stock='.$_POST['stock'];
            if(isset($_POST['saveIsCounted'])) $urlKeepExtension .= '&isCounted='.(isset($_POST['isCounted']) ? '1' : '0');
            if(isset($_POST['saveSidesignID'])) $urlKeepExtension .= '&sidesignID='.$_POST['sidesignID'];

            if($urlKeepExtension != "") $urlKeepExtension = '?keepEnabled'.$urlKeepExtension;
        }

        if(isset($_POST['expandSet']))
        {
            $setData = MySQL::Row("SELECT * FROM sets INNER JOIN bottlecaps ON sets.id = bottlecaps.setID WHERE sets.id = ?",'s',$_POST['expandSet']);

            $setID = $setData['setID'];

            $breweryID = $setData['breweryID'];
            $flavorID = $setData['flavorID'];
            $isSetsAndCollection = $setData['isSetsAndCollection'];

            $countryID = MySQL::Scalar("SELECT countryID FROM breweries WHERE id = ?",'s',$breweryID);
            $countryShort = MySQL::Scalar("SELECT countryShort FROM countries WHERE id = ?",'s',$countryID);

            $setFilepath = $setData['setFilepath'];

            $sqlStatement = "
            INSERT INTO bottlecaps
            (id, name, capNumber, flavorID, breweryID, sidesignID, baseColorID, capColorID, textColorID, isTraded, isUsed, isTwistlock, isTradeable, isCounted, isOwned, isSetsAndCollection, locationAquired, dateAquired, dateInserted, quality, stock,isSet,setID)
            VALUES
            (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1',?);";

            MySQL::NonQuery($sqlStatement,'@s',$name,$capNumber,$flavorID,$breweryID,$sidesignID,$baseColorID,$capColorID,$textColorID,$isTraded,$isUsed,$isTwistLock,$isTradeable,$isOwned,$isOwned,$isSetsAndCollection,$locationAquired,$dateAquired,$dateInserted,$quality,$stock,$setID);

            $capID = MySQL::Scalar("SELECT id FROM bottlecaps ORDER BY id DESC");

            $fileUploader->SetPath("files/sets/$countryShort/$setFilepath/");
            $fileUploader->SetSQLEntry("UPDATE bottlecaps SET capImage = '@FILENAME' WHERE id = '$capID'");

            $setSize = MySQL::Scalar("SELECT setSize FROM sets WHERE id = ?",'s',$setID);
            $setSize += 1;
            MySQL::NonQuery("UPDATE sets SET setSize = ? WHERE id = ?",'ss',$setSize,$setID);
        }

        if(isset($_POST['editBottlecap']))
        {
            $capEditData = MySQL::Row("SELECT * FROM bottlecaps WHERE id = ?",'s',$_POST['editBottlecap']);

            if($capEditData['isSet'])
            {
                // includes IsSetsAndCollection and isOwned (isOwned = isCounted)
                $setFilepath = MySQL::Scalar("SELECT setFilepath FROM sets WHERE id = ?",'s',$capEditData['setID']);
                $breweryID = MySQL::Scalar("SELECT breweryID FROM bottlecaps WHERE setID = ?",'s',$capEditData['setID']);
                $countryID = MySQL::Scalar("SELECT countryID FROM breweries WHERE id = ?",'s',$breweryID);
                $countryShort = MySQL::Scalar("SELECT countryShort FROM countries WHERE id = ?",'s',$countryID);

                $sqlStatement = "
                    UPDATE bottlecaps SET
                    name = ?,
                    capNumber = ?,
                    sidesignID = ?,
                    baseColorID = ?,
                    capColorID = ?,
                    textColorID = ?,
                    isTraded = ?,
                    isUsed = ?,
                    isTwistLock = ?,
                    isTradeable = ?,
                    isCounted = ?,
                    isOwned = ?,
                    locationAquired = ?,
                    dateAquired = ?,
                    quality = ?,
                    stock = ?
                    WHERE id = ?
                ";

                MySQL::NonQuery($sqlStatement,'@s',$name,$capNumber,$sidesignID,$baseColorID,$capColorID,$textColorID,$isTraded,$isUsed,$isTwistLock,$isTradeable,$isOwned,$isOwned,$locationAquired,$dateAquired,$quality,$stock,$_POST['editBottlecap']);

                $fileUploader->SetPath("files/sets/$countryShort/$setFilepath/");
                $fileUploader->SetSQLEntry("UPDATE bottlecaps SET capImage = '@FILENAME' WHERE id = '".$_POST['editBottlecap']."'");
            }
            else
            {
                // includes isCounted

                $sqlStatement = "
                    UPDATE bottlecaps SET
                    name = ?,
                    capNumber = ?,
                    flavorID = ?,
                    breweryID = ?,
                    sidesignID = ?,
                    baseColorID = ?,
                    capColorID = ?,
                    textColorID = ?,
                    isTraded = ?,
                    isUsed = ?,
                    isTwistLock = ?,
                    isTradeable = ?,
                    isCounted = ?,
                    locationAquired = ?,
                    dateAquired = ?,
                    quality = ?,
                    alcohol = ?,
                    stock = ?
                    WHERE id = ?
                ";

                MySQL::NonQuery($sqlStatement,'@s',$name,$capNumber,$flavorID,$breweryID,$sidesignID,$baseColorID,$capColorID,$textColorID,$isTraded,$isUsed,$isTwistLock,$isTradeable,$isCounted,$locationAquired,$dateAquired,$quality,$alcohol,$stock,$_POST['editBottlecap']);

                $fileUploader->SetPath("files/bottlecaps/$countryShort/$breweryFilepath/");
                $fileUploader->SetSQLEntry("UPDATE bottlecaps SET capImage = '@FILENAME' WHERE id = '".$_POST['editBottlecap']."'");
            }


        }

        $fileUploader->SetName($capNumber);
        $fileUploader->OverrideDuplicates(false);
        $fileUploader->Upload();

        if(isset($_POST['addBottlecap'])) Page::Redirect("/eintragen/kronkorken".$urlKeepExtension);
        else
        {
            echo '
                <script>
                    window.history.back();
                    window.history.back();
                </script>
            ';
        }
        die();
    }

    if(isset($_POST['editTradeCap']))
    {
        $capID = $_POST['editTradeCap'];

        $capData = MySQL::Row("SELECT * FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.id = ?",'s',$capID);
        $breweryFilepath = $capData['breweryFilepath'];
        $countryShort = $capData['countryShort'];
        $capNumber = $capData['capNumber'];
        $stock = $_POST['stock'];
        $quality = $_POST['quality'];

        MySQL::NonQuery("UPDATE bottlecaps SET stock = ?, qualityTrade = ? WHERE id = ?",'sss',$stock,$quality,$capID);

        $fileUploader = new FileUploader();
        $fileUploader->SetFileElement("capImage");
        $fileUploader->SetTargetResolution(1000,1000);

        if($capData['isSet'])
        {
            $setData = MySQL::Row("SELECT * FROM sets WHERE id = ?",'s',$capData['setID']);
            $setFilepath = $setData['setFilepath'];
            $fileUploader->SetPath("files/sets/$countryShort/$setFilepath/");
        }
        else $fileUploader->SetPath("files/bottlecaps/$countryShort/$breweryFilepath/");

        $fileUploader->SetSQLEntry("UPDATE bottlecaps SET capImageTrade = '@FILENAME' WHERE id = '$capID'");
        $fileUploader->SetName($capNumber.'-TR');
        $fileUploader->OverrideDuplicates(false);
        $fileUploader->Upload();


        echo '
            <script>
                window.history.back();
                window.history.back();
            </script>
        ';
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

    if(isset($_POST['addSetPart1']))
    {
        $breweryID = $_POST['breweryID'];
        $name = $_POST['setName'];
        $namePath = StringOp::SReplace($_POST['setName']);
        $flavorID = $_POST['flavorID'];
        $capNumberBase = $_POST['capNumberBase'];
        $setSize = $_POST['setSize'];

        $locationAquired = $_POST['locationAquired'];
        $dateAquired = $_POST['dateAquired'];
        $quality = $_POST['quality'];
        $isTraded = $_POST['isTraded'];
        $isTradeable = $_POST['isTradeable'];
        $alcohol = ($_POST['alcohol']=="") ? null : $_POST['alcohol'];

        $capColorID = explode('-',$_POST['capColorID'])[0];
        $baseColorID = explode('-',$_POST['baseColorID'])[0];
        $textColorID = explode('-',$_POST['textColorID'])[0];
        $isUsed = $_POST['isUsed'];
        $isTwistLock = $_POST['isTwistLock'];

        $isSetsAndCollection = isset($_POST['showInCollection']) ? 1 : 0;

        $sidesignID = $_POST['sidesignID'];

        $setTmpData = "breweryID=$breweryID;;flavorID=$flavorID;;capNumberBase=$capNumberBase;;locationAquired=$locationAquired;;dateAquired=$dateAquired;;quality=$quality;;isTraded=$isTraded;;isTradeable=$isTradeable;;alcohol=$alcohol;;capColorID=$capColorID;;baseColorID=$baseColorID;;textColorID=$textColorID;;isUsed=$isUsed;;isTwistLock=$isTwistLock;;isSetsAndCollection=$isSetsAndCollection;;sidesignID=$sidesignID";

        $sqlStatement  = "
        INSERT INTO sets
        (id,setSize,setName,setFilePath,setTmpData)
        VALUES
        (NULL,?,?,?,?)";

        MySQL::NonQuery($sqlStatement,'@s',$setSize,$name,$namePath,$setTmpData);

        $fileUploader = new FileUploader();
        $fileUploader->SetFileElement("capImages");
        $fileUploader->SetName("tmpSetUpload{#u}");
        $fileUploader->SetPath("files/sets/AUT/$namePath");
        $fileUploader->OverrideDuplicates(false);
        $fileUploader->Upload();


        Page::Redirect("/eintragen/set-konfigurieren?set=$namePath");
        die();
    }

    if(isset($_POST['addSetPart2']))
    {
        $setData = MySQL::Row("SELECT * FROM sets WHERE setFilepath = ?",'s',$_POST['addSetPart2']);

        $setDetailInfo = explode(';;',$setData['setTmpData']);
        $capData = array();
        for($i = 0 ; $i < count($setDetailInfo) ; $i ++)
        {
            $cd = explode('=',$setDetailInfo[$i]);
            $capData[str_replace(' ','',$cd[0])] = $cd[1];
        }

        $breweryData = MySQL::Row("SELECT * FROM breweries WHERE id = ?",'i',$capData['flavorID']);
        $countryData = MySQL::Row("SELECT * FROM countries WHERE id = ?",'i',$breweryData['countryID']);

        $sqlStatement = "
        INSERT INTO bottlecaps
        (id, name, capNumber, flavorID, breweryID, sidesignID, baseColorID, capColorID, textColorID, setID, isSet, isTraded, isUsed, isTwistlock, isTradeable, isCounted, isSetsAndCollection, isOwned, locationAquired, dateAquired, dateInserted, quality, alcohol, stock, capImage)
        VALUES ";

        $breweryID = $capData['breweryID'];
        $flavorID = $capData['flavorID'];

        $locationAquired = $capData['locationAquired'];
        $dateAquired = $capData['dateAquired'];
        $quality = $capData['quality'];
        $isTraded = $capData['isTraded'];
        $isTradeable = $capData['isTradeable'];
        $alcohol = ($capData['alcohol']=="") ? null : $capData['alcohol'];

        $isUsed = $capData['isUsed'];
        $isTwistLock = $capData['isTwistLock'];

        $isSetsAndCollection = $capData['isSetsAndCollection'];

        $sidesignID = $capData['sidesignID'];
        $dateInserted = date("Y-m-d");

        $setID = $setData['id'];


        $first = true;
        for($i = 0 ; $i < $setData['setSize'] ; $i++)
        {
            $capNumber = $_POST['capNumber'.$i];
            $capName = $_POST['capName'.$i];
            $stock = $_POST['stock'.$i];
            $capColorID = $_POST['capColor'.$i];
            $baseColorID = $_POST['baseColor'.$i];
            $textColorID = $_POST['textColor'.$i];
            $isOwned = isset($_POST['isOwned'.$i]) ? 1 : 0;

            $originalImage = ltrim($_POST['capImage'.$i],'/');
            $fileExtension = pathinfo($originalImage, PATHINFO_EXTENSION);
            rename($originalImage,'files/sets/'.$countryData['countryShort'].'/'.$setData['setFilepath'].'/'.$capNumber.'.'.$fileExtension);
            $capImage = $capNumber.'.'.$fileExtension;

            if($first)
            {
                $sqlStatement .= "(NULL,'$capName','$capNumber','$flavorID','$breweryID','$sidesignID','$baseColorID','$capColorID','$textColorID','$setID','1','$isTraded','$isUsed','$isTwistLock','$isTradeable','$isOwned','$isSetsAndCollection','$isOwned','$locationAquired','$dateAquired','$dateInserted','$quality',".($alcohol == null ? "NULL" : "'$alcohol'").",'$stock','$capImage')";
                $first = false;
            }
            else $sqlStatement .= ",(NULL,'$capName','$capNumber','$flavorID','$breweryID','$sidesignID','$baseColorID','$capColorID','$textColorID','$setID','1','$isTraded','$isUsed','$isTwistLock','$isTradeable','$isOwned','$isSetsAndCollection','$isOwned','$locationAquired','$dateAquired','$dateInserted','$quality',".($alcohol == null ? "NULL" : "'$alcohol'").",'$stock','$capImage')";
        }

        MySQL::NonQuery($sqlStatement);

        // Add thumbnail to set
        $thumbnail = MySQL::Scalar("SELECT id FROM bottlecaps WHERE setID = ?",'s',$setID);
        MySQL::NonQuery("UPDATE sets SET thumbnailID = ?, thumbnailTradeID = ?,  WHERE id = ?",'ss',$thumbnail,$thumbnail,$setID);

        Page::Redirect('/sets/'.$countryData['countryShort'].'/'.$setData['setFilepath']);
        die();
    }

    if(isset($_POST['editSetGeneral']))
    {
        $setID = $_POST['editSetGeneral'];

        $breweryID = $_POST['breweryID'];
        $name = $_POST['setName'];
        $flavorID = $_POST['flavorID'];
        $capNumberBase = $_POST['capNumberBase'];
        $showInCollection = isset($_POST['showInCollection']) ? 1 : 0;
        $alcohol = ($_POST['alcohol']=="") ? null : $_POST['alcohol'];

        MySQL::NonQuery("UPDATE sets SET setName = ? WHERE id = ?",'ss',$name,$setID);

        $setCaps = MySQL::Cluster("SELECT * FROM bottlecaps WHERE setID = ?",'s',$setID);

        foreach($setCaps AS $setCap)
        {
            $oldCapNumber = $setCap['capNumber'];
            $capExtension = substr($oldCapNumber,strrpos($oldCapNumber,'_') + 1,strlen($oldCapNumber));
            $newCapNumber = $capNumberBase.'_'.$capExtension;

            MySQL::NonQuery("UPDATE bottlecaps SET capNumber = ?, breweryID = ?, flavorID = ?, alcohol = ?, isSetsAndCollection = ?  WHERE id = ?",'@s',$newCapNumber,$breweryID,$flavorID,$alcohol,$showInCollection,$setCap['id']);
        }

        Page::Redirect(Page::This());
        die();
    }

    if(isset($_POST['editSetDetails']))
    {
        $setID = $_POST['editSetDetails'];

        $capColorID = $_POST['capColorID'];
        $baseColorID = $_POST['baseColorID'];
        $textColorID = $_POST['textColorID'];
        $locationAquired = $_POST['locationAquired'];
        $dateAquired = $_POST['dateAquired'];
        $quality = $_POST['quality'];
        $isTraded = $_POST['isTraded'];
        $isTradeable = $_POST['isTradeable'];
        $isUsed = $_POST['isUsed'];
        $isTwistLock = $_POST['isTwistLock'];

        $setCaps = MySQL::Cluster("SELECT * FROM bottlecaps WHERE setID = ?",'s',$setID);

        foreach($setCaps AS $setCap)
        {
            MySQL::NonQuery("UPDATE bottlecaps SET capColorID = ?, baseColorID = ?, textColorID = ?, locationAquired = ?, dateAquired = ?, quality = ?, isTraded = ?, isTradeable = ?, isUsed = ?, isTwistlock = ? WHERE id = ?",'@s',$capColorID,$baseColorID,$textColorID,$locationAquired,$dateAquired,$quality,$isTraded,$isTradeable,$isUsed,$isTwistLock,$setCap['id']);
        }

        Page::Redirect(Page::This());
        die();
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
            $isSetPart = false;
            if(isset($_GET['edit']))
            {
                $edit = true;
                $isSetPart = MySQL::Scalar("SELECT isSet FROM bottlecaps WHERE id = ?",'s',$_GET['objID']);
            }
            else $edit = false;

            if(isset($_GET['expand']))
            {
                $isSetPart = true;
                $expand = true;
            }
            else $expand = false;

            if($edit) echo '<h2>Kronkorken bearbeiten</h2>';
            else if($expand) echo '<h2>Set erweitern</h2>';
            else echo '<h2>Kronkorken hinzuf&uuml;gen</h2>';


            $countryList = MySQL::Cluster("SELECT * FROM countries RIGHT JOIN breweries ON countries.id = breweries.countryID GROUP BY breweries.countryID ORDER BY countries.countryDE ASC");
            $flavorList = MySQL::Cluster("SELECT * FROM flavors");
            $colorList = MySQL::Cluster("SELECT * FROM colors");
            $sidesignFrequentList = MySQL::Cluster("SELECT *,COUNT(sidesignID) AS sidesignCount FROM bottlecaps INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id GROUP BY sidesignID HAVING sidesignCount >= 3");
            $sidesignAllList = MySQL::Cluster("SELECT * FROM sidesigns ORDER BY sidesignName ASC");
            $qualityValueList = array("A" => "A","B" => "B","C" => "C","D" => "D","E" => "E");
            $qualityDisplayList = array("A" => "A - Neu","B" => "B - Benutzt, Sehr guter Zustand","C" => "C - Benutzt, kleine Kratzer/Knicke","D" => "D - Benutzt, gro&szlig;e Kratzer/Knicke","E" => "E - Benutzt, schlechter zust.");

            if($edit)
            {
                echo '
                    <script type="text/javascript">
                        $(document).ready(function() {
                            InsertCapUpdateCapPreview();
                        });
                    </script>
                ';
                $capData = MySQL::Row("SELECT *,bottlecaps.id AS bottlecapID FROM bottlecaps INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE bottlecaps.id = ?",'s',$_GET['objID']);
                if($capData['isSet'])
                {
                    $setData = MySQL::Row("SELECT * FROM sets WHERE id = ?",'s',$capData['setID']);
                    $capDataImage = '/files/sets/'.$capData['countryShort'].'/'.$setData['setFilepath'].'/'.$capData['capImage'];
                }
                else $capDataImage = '/files/bottlecaps/'.$capData['countryShort'].'/'.$capData['breweryFilepath'].'/'.$capData['capImage'];
            }

            if(isset($_GET['keepEnabled']))
            {
                echo '
                    <script type="text/javascript">
                        $(document).ready(function() {
                            InsertCapUpdateCapPreview();
                        });
                    </script>
                ';
            }

            if($expand)
            {
                $setData = MySQL::Row("SELECT * FROM sets INNER JOIN bottlecaps ON sets.id = bottlecaps.setID INNER JOIN breweries ON bottlecaps.breweryID = breweries.id INNER JOIN countries ON breweries.countryID = countries.id WHERE sets.id = ?",'s',$_GET['objID']);
            }

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
                                    <select tabindex="1" '.($isSetPart ? 'disabled' : '').' name="countryID" class="cel_100 cef_nomg cef_nopd" id="countryList" onchange="DynLoadList(1,this,\'--- Ausw\u00e4hlen ---\',\'breweryList\',\'SELECT breweryName AS dynLoadText, id AS dynLoadValue FROM breweries WHERE countryID = ?? ORDER BY breweryName ASC\'); DynLoadScalar(2,this,\'outCountryShort\',\'SELECT countryShort2 FROM countries WHERE id = ??\')">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($countryList AS $country) echo '<option value="'.$country['countryID'].'" '.((($expand AND $country['countryID'] == $setData['countryID']) OR ($edit AND $country['countryID'] == $capData['countryID']) OR (isset($_GET['countryID']) AND $country['countryID'] == $_GET['countryID'])) ? 'selected' : '').'>'.$country['countryDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveCountryID","saveCountryID","",(isset($_GET['keepEnabled']) ? (isset($_GET['countryID']) ? true : false) : true)).'</td>

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
                                        <img src="'.($edit ? $capDataImage : '').'" alt="" id="capImagePreview"/><br>
                                        '.FileButton("capImage","capImage",false,"ReadURL(this,'capImagePreview');","","width: 100px; line-height: 5px;",false,($edit ? $capDataImage : '')).'
                                    </center>
                                </td>

                            </tr>

                            <tr>
                                <td>Brauerei</td>
                                <td>
                                    <select tabindex="2" '.($isSetPart ? 'disabled' : '').' name="breweryID" class="cel_m cef_nomg cef_nopd" id="breweryList" required
                                    onchange="DynLoadScalar(3,this,\'outBreweryShort\',\'SELECT breweryShort FROM breweries WHERE id = ??\'); CopyShortsToCapNumber(false);"
                                    onclick="DynLoadScalar(3,this,\'outBreweryShort\',\'SELECT breweryShort FROM breweries WHERE id = ??\'); CopyShortsToCapNumber(false)">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        '.($expand ? ('<option value="'.$setData['breweryID'].'" selected>'.$setData['breweryName'].'</option>') : '').'
                                        '.($edit ? ('<option value="'.$capData['breweryID'].'" selected>'.$capData['breweryName'].'</option>') : '').'
                                        '.(isset($_GET['breweryID']) ? ('<option value="'.$_GET['breweryID'].'" selected>'.MySQL::Scalar("SELECT breweryName FROM breweries WHERE id = ?",'s',$_GET['breweryID']).'</option>') : '').'

                                    </select>
                                </td>
                                <td>'.Tickbox("saveBreweryID","saveBreweryID","",(isset($_GET['keepEnabled']) ? (isset($_GET['breweryID']) ? true : false) : true)).'</td>
                            </tr>

                            <tr>
                                <td>Name</td>
                                <td><input tabindex="3" class="cel_m" type="text" name="name" placeholder="Name..." value="'.($edit ? $capData['name'] : (isset($_GET['name']) ? $_GET['name'] : '')).'" required/></td>
                                <td>'.Tickbox("saveName","saveName","",(isset($_GET['keepEnabled']) ? (isset($_GET['name']) ? true : false) : true)).'</td>
                            </tr>

                            <tr>
                                <td>Sorte</td>
                                <td>
                                    <select tabindex="4" '.($isSetPart ? 'disabled' : '').' name="flavorID" class="cel_100 cef_nomg cef_nopd" id="" required>
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($flavorList AS $flavor) echo '<option value="'.$flavor['id'].'" '.((($edit AND $flavor['id'] == $capData['flavorID']) OR (isset($_GET['flavorID']) AND $_GET['flavorID'] == $flavor['id'])) ? 'selected' : '').'>'.$flavor['flavorDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveFlavorID","saveFlavorID","",(isset($_GET['keepEnabled']) ? (isset($_GET['flavorID']) ? true : false) : true)).'</td>
                            </tr>

                            <tr>
                                ';

                                if($expand) $capNumberBase = (substr($setData['capNumber'],0,strrpos($setData['capNumber'],'_')).'_');
                                if(isset($_GET['capNumber']))  $capNumberBase = (substr($_GET['capNumber'],0,strrpos($_GET['capNumber'],'_')).'_');

                                echo '

                                <td>Kapsel-Nr.'.($edit ? ('<br><sub><span style="color: #696969">Original: '.$capData['capNumber'].'</span></sub>') : (isset($_GET['capNumber']) ? ('<br><sub><span style="color: #696969">Letztes: '.$_GET['capNumber'].'</span></sub>') : '')).'</td>
                                <td><input tabindex="5" class="cel_m" type="text" name="capNumber" id="capNumber" placeholder="XX_XX_XXXX" value="'.($edit ? $capData['capNumber'] : ''.(($expand OR isset($_GET['capNumber'])) ? $capNumberBase : '')).'" required onclick="CopyShortsToCapNumber(true)"/></td>
                                <td>'.Tickbox("saveCapNumber","saveCapNumber","",(isset($_GET['keepEnabled']) ? (isset($_GET['capNumber']) ? true : false) : true)).'</td>
                            </tr>

                            <tr>
                                <td colspan=3>Zusatzinfos</td>
                                <td colspan=3>Optische angaben</td>
                            </tr>

                            <tr>
                                <td>Erhaltsort</td>
                                <td><input tabindex="6" class="cel_m" type="text" name="locationAquired" placeholder="Erhaltsort..."  value="'.($edit ? $capData['locationAquired'] : (isset($_GET['locationAquired']) ? $_GET['locationAquired'] : '')).'"/></td>
                                <td>'.Tickbox("saveLocationAquired","saveLocationAquired","",(isset($_GET['keepEnabled']) ? (isset($_GET['locationAquired']) ? true : false) : true)).'</td>

                                <td>Kapselfarbe</td>
                                <td>
                                    <select tabindex="9" name="capColorID" class="cel_100 cef_nomg cef_nopd" id="capColor" onchange="InsertCapUpdateCapPreview()">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($colorList AS $color) echo '<option style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'-'.$color['colorShort'].'" '.((($edit AND $color['id'] == $capData['capColorID']) OR (isset($_GET['capColorID']) AND $_GET['capColorID'] == $color['id'])) ? 'selected' : '').'>'.$color['colorDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveCapColorID","saveCapColorID","",(isset($_GET['keepEnabled']) ? (isset($_GET['capColorID']) ? true : false) : true)).'</td>
                            </tr>

                            <tr>
                                <td>Erhaltsdatum</td>
                                <td><input tabindex="7" class="cel_m" type="text" name="dateAquired" placeholder="Erhaltsdatum..." value="'.($edit ? $capData['dateAquired'] : (isset($_GET['keepEnabled']) ? $_GET['dateAquired'] : '')).'"/></td>
                                <td>'.Tickbox("saveDateAquired","saveDateAquired","",(isset($_GET['keepEnabled']) ? (isset($_GET['dateAquired']) ? true : false) : true)).'</td>

                                <td>Grundfarbe</td>
                                <td>
                                    <select tabindex="10" name="baseColorID" class="cel_100 cef_nomg cef_nopd" id="baseColor" onchange="InsertCapUpdateCapPreview()">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($colorList AS $color) echo '<option style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'-'.$color['hex'].'" '.((($edit AND $color['id'] == $capData['baseColorID']) OR (isset($_GET['baseColorID']) AND $_GET['baseColorID'] == $color['id'])) ? 'selected' : '').'>'.$color['colorDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveBaseColorID","saveBaseColorID","",(isset($_GET['keepEnabled']) ? (isset($_GET['baseColorID']) ? true : false) : true)).'</td>
                            </tr>

                            <tr>
                                <td>Qualit&auml;t</td>
                                <td>
                                    <select tabindex="8" name="quality" class="cel_m cef_nomg cef_nopd" id="">
                                        <option value="" selected>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($qualityValueList AS $quality) echo '<option value="'.$quality.'"  '.((($edit AND $quality == $capData['quality']) OR (isset($_GET['quality']) AND $_GET['quality'] == $quality)) ? 'selected' : '').'>'.$qualityDisplayList[$quality].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveQuality","saveQuality","",(isset($_GET['keepEnabled']) ? (isset($_GET['quality']) ? true : false) : true)).'</td>

                                <td>Textfarbe</td>
                                <td>
                                    <select tabindex="11" name="textColorID" class="cel_100 cef_nomg cef_nopd" id="textColor" onchange="InsertCapUpdateCapPreview()">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($colorList AS $color) echo '<option style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'-'.$color['hex'].'" '.((($edit AND $color['id'] == $capData['textColorID']) OR (isset($_GET['textColorID']) AND $_GET['textColorID'] == $color['id'])) ? 'selected' : '').'>'.$color['colorDE'].'</option>';
                                        echo '
                                    </select>
                                </td>
                                <td>'.Tickbox("saveTextColorID","saveTextColorID","",(isset($_GET['keepEnabled']) ? (isset($_GET['textColorID']) ? true : false) : true)).'</td>
                            </tr>

                            <tr>
                                <td>Erhalten durch</td>
                                <td>
                                    <table>
                                        <tr>
                                            ';

                                            if($edit)
                                            {
                                                if($capData['isTraded'] == 1) $isTradedValue1 = true;
                                                else $isTradedValue1 = false;
                                            }
                                            else if(isset($_GET['isTraded']))
                                            {
                                                if($_GET['isTraded'] == 1) $isTradedValue1 = true;
                                                else $isTradedValue1 = false;
                                            }
                                            else $isTradedValue1 = false;

                                            $isTradeValue2 = !$isTradedValue1;

                                            echo '
                                            <td>'.RadioButton("Tausch","isTraded",$isTradedValue1,"1").'</td>
                                            <td>'.RadioButton("Kauf","isTraded",$isTradeValue2,"0").'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>'.Tickbox("saveIsTraded","saveIsTraded","",(isset($_GET['keepEnabled']) ? (isset($_GET['isTraded']) ? true : false) : true),"").'</td>

                                <td>Zustand</td>
                                <td>
                                    <table>
                                        <tr>
                                            ';

                                            if($edit)
                                            {
                                                if($capData['isUsed'] == 1) $isUsedValue1 = true;
                                                else $isUsedValue1 = false;
                                            }
                                            else if(isset($_GET['isUsed']))
                                            {
                                                if($_GET['isUsed'] == 1) $isUsedValue1 = true;
                                                else $isUsedValue1 = false;
                                            }
                                            else $isUsedValue1 = true;

                                            $isUsedValue2 = !$isUsedValue1;

                                            echo '
                                            <td>'.RadioButton("Neu","isUsed",$isUsedValue2,"0","InsertCapUpdateCapPreview()").'</td>
                                            <td>'.RadioButton("Gebr.","isUsed",$isUsedValue1,"1","InsertCapUpdateCapPreview()").'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>'.Tickbox("saveIsUsed","saveIsUsed","",(isset($_GET['keepEnabled']) ? (isset($_GET['isUsed']) ? true : false) : true),"").'</td>
                            </tr>

                            <tr>
                                <td>Tauschbar</td>
                                <td>
                                    <table>
                                        <tr>
                                            ';

                                            if($edit)
                                            {
                                                if($capData['isTradeable'] == 1) $isTradeableValue1 = true;
                                                else $isTradeableValue1 = false;
                                            }
                                            else if(isset($_GET['isTradeable']))
                                            {
                                                if($_GET['isTradeable'] == 1) $isTradeableValue1 = true;
                                                else $isUsedValue1 = false;
                                            }
                                            else $isTradeableValue1 = false;

                                            $isTradeableValue2 = !$isTradeableValue1;

                                            echo '
                                            <td>'.RadioButton("Ja","isTradeable",$isTradeableValue1,"1").'</td>
                                            <td>'.RadioButton("Nein","isTradeable",$isTradeableValue2,"0").'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>'.Tickbox("saveIsTradeable","saveIsTradeable","",(isset($_GET['keepEnabled']) ? (isset($_GET['isTradeable']) ? true : false) : true)).'</td>

                                <td>Drehverschluss</td>
                                <td>
                                    <table>
                                        <tr>
                                            ';

                                            if($edit)
                                            {
                                                if($capData['isTwistlock'] == 1) $isTwistlockValue1 = true;
                                                else $isTwistlockValue1 = false;
                                            }
                                            else if(isset($_GET['isTwistlock']))
                                            {
                                                if($_GET['isTwistlock'] == 1) $isTwistlockValue1 = true;
                                                else $isTwistlockValue1 = false;
                                            }
                                            else $isTwistlockValue1 = false;

                                            $isTwistlockValue2 = !$isTwistlockValue1;

                                            echo '
                                            <td>'.RadioButton("Ja","isTwistLock",$isTwistlockValue1,"1","InsertCapUpdateCapPreview()").'</td>
                                            <td>'.RadioButton("Nein","isTwistLock",$isTwistlockValue2,"0","InsertCapUpdateCapPreview()").'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>'.Tickbox("saveIsTwistLock","saveIsTwistLock","",(isset($_GET['keepEnabled']) ? (isset($_GET['isTwistLock']) ? true : false) : true)).'</td>
                            </tr>

                            <tr>
                                <td>Alkoholgehalt</td>
                                <td><input tabindex="12" '.($isSetPart ? 'disabled' : '').' class="cel_m" type="number" step="0.1" name="alcohol" placeholder="Alkoholgehalt..." value="'.($edit ? (($capData['alcohol']!=NULL) ? $capData['alcohol'] : '' ) : (isset($_GET['alcohol']) ? $_GET['alcohol'] : '')).'" /></td>
                                <td>'.Tickbox("saveAlcohol","saveAlcohol","",(isset($_GET['keepEnabled']) ? (isset($_GET['alcohol']) ? true : false) : true)).'</td>


                            </tr>

                            <tr>
                                <td>Auf Lager</td>
                                <td><input tabindex="13" class="cel_m" type="number" step="1" name="stock" placeholder="Auf Lager..." value="'.($edit ? $capData['stock'] : (isset($_GET['stock']) ? $_GET['stock'] : '')).'"/></td>
                                <td>'.Tickbox("saveStock","saveStock","",(isset($_GET['keepEnabled']) ? (isset($_GET['stock']) ? true : false) : true)).'</td>

                                ';

                                if($edit AND $capData['isSet'])
                                {
                                    echo '
                                        <td>In Besitz</td>
                                        <td>'.Tickbox("isOwned","isOwned","",($edit ? ($capData['isOwned']==1 ? true : false) : true)).'</td>
                                        <td>'.Tickbox("saveIsCounted","saveIsCounted","",true).'</td>
                                    ';
                                }
                                else if($expand)
                                {
                                    echo '
                                        <td>In Besitz</td>
                                        <td>'.Tickbox("isOwned","isOwned","",true).'</td>
                                        <td>'.Tickbox("saveIsCounted","saveIsCounted","",true).'</td>
                                    ';
                                }
                                else
                                {
                                    echo '
                                        <td>Mitz&auml;hlen</td>
                                        <td>'.Tickbox("isCounted","isCounted","",($edit ? ($capData['isCounted']==1 ? true : false) : true)).'</td>
                                        <td>'.Tickbox("saveIsCounted","saveIsCounted","",(isset($_GET['isCounted']) ? true : false)).'</td>
                                    ';
                                }

                                echo '
                            </tr>

                            <tr>
                                <td colspan=7>
                                    <br>
                                    ';

                                    if($edit) echo '<button type="submit" name="editBottlecap" value="'.$capData['bottlecapID'].'">Kronkorken aktualisieren</button>';
                                    else if($expand) echo '<button type="submit" name="expandSet" value="'.$setData['setID'].'">Kronkorken zu Set hinzuf&uuml;gen</button>';
                                    else echo '<button type="submit" name="addBottlecap">Kronkorken hinzuf&uuml;gen</button>';

                                    echo '
                                </td>
                            </tr>
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
                                            <input type="radio" id="sidesignAll'.$sidesign['id'].'" value="'.$sidesign['id'].'" name="sidesignID" hidden required '.((($edit AND $sidesign['id'] == $capData['sidesignID']) OR (isset($_GET['sidesignID']) AND $_GET['sidesignID'] == $sidesign['id'])) ? 'checked' : '').'/>
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


                        <table class="addCapSidesignTable">
                            <tr>
                                <td>Randzeichen</td>
                                <td>'.Tickbox("saveSidesignID","saveSidesignID","",(isset($_GET['keepEnabled']) ? (isset($_GET['sidesignID']) ? true : false) : true)).'</td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                ';
                                foreach($sidesignFrequentList AS $sidesign)
                                {
                                    echo '
                                        <input type="radio" id="sidesignFrequent'.$sidesign['sidesignID'].'" value="'.$sidesign['sidesignID'].'" name="sidesignID" hidden required '.((($edit AND $sidesign['sidesignID'] == $capData['sidesignID']) OR (isset($_GET['sidesignID']) AND $_GET['sidesignID'] == $sidesign['id'])) ? 'checked' : '').'/>
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
                            <tr><td colspan=2><a href="#allSidesigns">Alle Randzeichen</a></td></tr>
                        </table>

                    </form>
                </center>
            ';
        }

        if($_GET['section'] == 'tauschkronkorken')
        {
            $capData = MySQL::Row("SELECT * FROM bottlecaps WHERE id = ?",'s',$_GET['objID']);
            $qualityValueList = array("A" => "A","B" => "B","C" => "C","D" => "D","E" => "E");
            $qualityDisplayList = array("A" => "A - Neu","B" => "B - Benutzt, Sehr guter Zustand","C" => "C - Benutzt, kleine Kratzer/Knicke","D" => "D - Benutzt, gro&szlig;e Kratzer/Knicke","E" => "E - Benutzt, schlechter zust.");

            echo '<h2>Tausch-Kronkorken bearbeiten</h2>';

            echo '
                <br>
                <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <center>
                        <table class="editTradeCap">
                            <tr>
                                <td colspan=2>Allgemeines</td>
                                <td rowspan=3></td>
                                <td>Bild f&uuml;r Tausch</td>
                            </tr>

                            <tr>
                                <td>Auf Lager:</td>
                                <td>
                                    <input class="cel_m" type="number" step="1" name="stock" placeholder="Auf Lager..." value="'.$capData['stock'].'"/>
                                </td>
                                <td colspan=2 rowspan=2>
                                    <center>
                                        <img src="" alt="" id="capImagePreview"/><br>
                                        '.FileButton("capImage","capImages",true,"ReadURL(this,'capImagePreview');","","width: 100px; line-height: 5px;",false).'
                                    </center>
                                </td>
                            </tr>
                            <tr>
                                <td>Qualit&auml;t</td>
                                <td>
                                    <select tabindex="8" name="quality" class="cel_m cef_nomg cef_nopd" id="">
                                        <option value="" selected>--- Ausw&auml;hlen ---</option>
                                        ';
                                        foreach($qualityValueList AS $quality) echo '<option value="'.$quality.'"  '.((($edit AND $quality == $capData['quality']) OR (isset($_GET['quality']) AND $_GET['quality'] == $quality)) ? 'selected' : '').'>'.$qualityDisplayList[$quality].'</option>';
                                        echo '
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=7>
                                    <br>
                                    <button type="submit" name="editTradeCap" value="'.$_GET['objID'].'">Kronkorken aktualisieren</button>
                                </td>
                            </tr>
                        </table>
                    </center>
                </form>
            ';
        }

//========================================================================================
//========================================================================================
//      ADD SET
//========================================================================================
//========================================================================================

        if($_GET['section'] == 'set')
        {
            if(isset($_GET['edit'])) $edit = true;
            else $edit = false;

            if($edit) echo '<h2>Set bearbeiten</h2>';
            else echo '<h2>Set hinzuf&uuml;gen</h2>';

            $countryList = MySQL::Cluster("SELECT * FROM countries RIGHT JOIN breweries ON countries.id = breweries.countryID GROUP BY breweries.countryID ORDER BY countries.countryDE ASC");
            $flavorList = MySQL::Cluster("SELECT * FROM flavors");
            $colorList = MySQL::Cluster("SELECT * FROM colors");
            $sidesignFrequentList = MySQL::Cluster("SELECT *,COUNT(sidesignID) AS sidesignCount FROM bottlecaps INNER JOIN sidesigns ON bottlecaps.sidesignID = sidesigns.id GROUP BY sidesignID HAVING sidesignCount >= 3");
            $sidesignAllList = MySQL::Cluster("SELECT * FROM sidesigns ORDER BY sidesignName ASC");
            $qualityValueList = array("A" => "A","B" => "B","C" => "C","D" => "D","E" => "E");
            $qualityDisplayList = array("A" => "A - Neu","B" => "B - Benutzt, Sehr guter Zustand","C" => "C - Benutzt, kleine Kratzer/Knicke","D" => "D - Benutzt, gro&szlig;e Kratzer/Knicke","E" => "E - Benutzt, schlechter zust.");

            if(!$edit)
            {
                echo '
                    <center>
                        <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">

                            <table class="addSet1Table">
                                <tr>
                                    <td colspan=2>Allgemeines</td>
                                    <td rowspan=13></td>
                                    <td colspan=2>Bilder</td>
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

                                    <td rowspan=6>
                                        <center>
                                            <div class="bottlecapColorSchemeContainerEntry">
                                                <div><img name="capPreviewCapColor" id="GLD" src="/content/capUsedColored.png" alt="" /></div>
                                                <div name="capPreviewBaseColor" style="background: #FFFFFF"></div>
                                                <div name="capPreviewTextColor" style="color: #FF0000">K-K-D</div>
                                                <div name="capPreviewTwistLock"></div>
                                            </div>
                                        </center>
                                    </td>
                                    <td rowspan=6 colspan=2>
                                        <center>
                                            <img src="" alt="" id="capImagePreview"/><br>
                                            '.FileButton("capImages","capImages",true,"ReadURL(this,'capImagePreview');","","width: 100px; line-height: 5px;",true).'
                                        </center>
                                    </td>

                                </tr>

                                <tr>
                                    <td>Brauerei</td>
                                    <td>
                                        <select name="breweryID" class="cel_m cef_nomg cef_nopd" id="breweryList" required>
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Set-Name</td>
                                    <td><input class="cel_m" type="text" name="setName" placeholder="Set-Name..." required/></td>
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
                                </tr>

                                <tr>
                                    <td>Set-K&uuml;rzel</td>
                                    <td><input class="cel_m" type="text" name="capNumberBase" placeholder="z.B.: AT_XX_S01" required onclick="CopyShortsToCapNumber(true)"/></td>
                                </tr>
                                <tr>
                                    <td>Set-Gr&ouml;&szlig;e</td>
                                    <td><input class="cel_m" type="number" name="setSize" placeholder="Anzahl..." required/></td>
                                </tr>
                                <tr>
                                    <td colspan=2>Zusatzinfos</td>
                                    <td colspan=2>Optische angaben</td>
                                </tr>

                                <tr>
                                    <td>Erhaltsort</td>
                                    <td><input class="cel_m" type="text" name="locationAquired" placeholder="Erhaltsort..."/></td>

                                    <td>Kapselfarbe</td>
                                    <td>
                                        <select name="capColorID" class="cel_100 cef_nomg cef_nopd" id="capColor" onchange="InsertCapUpdateCapPreview()">
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                            ';
                                            foreach($colorList AS $color) echo '<option style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'-'.$color['colorShort'].'">'.$color['colorDE'].'</option>';
                                            echo '
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Erhaltsdatum</td>
                                    <td><input class="cel_m" type="text" name="dateAquired" placeholder="Erhaltsdatum..."/></td>

                                    <td>Grundfarbe</td>
                                    <td>
                                        <select name="baseColorID" class="cel_100 cef_nomg cef_nopd" id="baseColor" onchange="InsertCapUpdateCapPreview()">
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                            ';
                                            foreach($colorList AS $color) echo '<option style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'-'.$color['hex'].'">'.$color['colorDE'].'</option>';
                                            echo '
                                        </select>
                                    </td>
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

                                    <td>Textfarbe</td>
                                    <td>
                                        <select name="textColorID" class="cel_100 cef_nomg cef_nopd" id="textColor" onchange="InsertCapUpdateCapPreview()">
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                            ';
                                            foreach($colorList AS $color) echo '<option style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'-'.$color['hex'].'">'.$color['colorDE'].'</option>';
                                            echo '
                                        </select>
                                    </td>
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

                                    <td>Zustand</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>'.RadioButton("Neu","isUsed",false,"0","InsertCapUpdateCapPreview()").'</td>
                                                <td>'.RadioButton("Gebr.","isUsed",true,"1","InsertCapUpdateCapPreview()").'</td>
                                            </tr>
                                        </table>
                                    </td>
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

                                    <td>Drehverschluss</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>'.RadioButton("Ja","isTwistLock",false,"1","InsertCapUpdateCapPreview()").'</td>
                                                <td>'.RadioButton("Nein","isTwistLock",true,"0","InsertCapUpdateCapPreview()").'</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Alkoholgehalt</td>
                                    <td><input class="cel_m" type="number" step="0.1" name="alcohol" placeholder="Alkoholgehalt..."/></td>
                                </tr>
                                <tr>
                                    <td>In Sets & Sammlung<br>zeigen</td>
                                    <td style="text-align:left; padding-left: 10px;" >
                                        '.Tickbox("showInCollection","showInCollection","&nbsp;&nbsp;Ja",false).'
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=7>
                                        <br>
                                        <button type="submit" name="addSetPart1">Weiter &#10148;</button>
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
            else
            {
                $setData = MySQL::Row("SELECT * FROM sets INNER JOIN bottlecaps ON sets.id = bottlecaps.setID INNER JOIN breweries ON bottlecaps.breweryID = breweries.id WHERE sets.id = ?",'s',$_GET['objID']);

                echo '
                    <center>
                        <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <table class="editSetTable">
                                <tr>
                                    <td colspan=2>Allgemeine Set-Daten</td>
                                </tr>

                                <tr>
                                    <td>Land</td>
                                    <td>
                                        <select name="countryID" class="cel_100 cef_nomg cef_nopd" id="countryList" onchange="DynLoadList(1,this,\'--- Ausw\u00e4hlen ---\',\'breweryList\',\'SELECT breweryName AS dynLoadText, id AS dynLoadValue FROM breweries WHERE countryID = ?? ORDER BY breweryName ASC\'); DynLoadScalar(2,this,\'outCountryShort\',\'SELECT countryShort2 FROM countries WHERE id = ??\')">
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                            ';
                                            foreach($countryList AS $country) echo '<option '.($country['countryID'] == $setData['countryID'] ? 'selected' : '').' value="'.$country['countryID'].'">'.$country['countryDE'].'</option>';
                                            echo '
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Brauerei</td>
                                    <td>
                                        <select name="breweryID" class="cel_m cef_nomg cef_nopd" id="breweryList" required>
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                            <option value="'.$setData['breweryID'].'" selected>'.$setData['breweryName'].'</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Set-Name</td>
                                    <td><input class="cel_m" type="text" name="setName" placeholder="Set-Name..." value="'.$setData['setName'].'" required/></td>
                                </tr>

                                <tr>
                                    <td>Sorte</td>
                                    <td>
                                        <select name="flavorID" class="cel_100 cef_nomg cef_nopd" id="" required>
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                            ';
                                            foreach($flavorList AS $flavor) echo '<option '.($flavor['id'] == $setData['flavorID'] ? 'selected' : '').' value="'.$flavor['id'].'">'.$flavor['flavorDE'].'</option>';
                                            echo '
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Set-K&uuml;rzel</td>
                                    <td><input class="cel_m" type="text" name="capNumberBase" placeholder="XX_XX_XXXX" value="'.substr($setData['capNumber'],0,strrpos($setData['capNumber'],'_')).'" required onclick="CopyShortsToCapNumber(true)"/></td>
                                </tr>

                                <tr>
                                    <td>Alkoholgehalt</td>
                                    <td><input class="cel_m" type="number" step="0.1" name="alcohol" placeholder="Alkoholgehalt..." value="'.$setData['alcohol'].'"/></td>
                                </tr>

                                <tr>
                                    <td>In Sets & Sammlung<br>zeigen</td>
                                    <td style="text-align:left; padding-left: 10px;" >
                                        '.Tickbox("showInCollection","showInCollection","&nbsp;&nbsp;Ja",$setData['isSetsAndCollection']).'
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan=2>
                                        <br>
                                        <button type="submit" name="editSetGeneral" value="'.$_GET['objID'].'">Set-Daten aktualisieren</button>
                                    </td>
                                </tr>
                            </table>

                            <br>

                            <table class="editSetTable">
                                <tr>
                                    <td colspan=2>Erweiterte Set-Daten<br><span style="font-size: 10pt;">Achtung: &Uuml;berschreibt alle<br>individuell eingetragenen Werte</span></td>
                                </tr>

                                <tr>
                                    <td>Kapselfarbe</td>
                                    <td>
                                        <select name="capColorID" class="cel_100 cef_nomg cef_nopd" id="capColor" onchange="InsertCapUpdateCapPreview()">
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                            ';
                                            foreach($colorList AS $color) echo '<option '.($color['id'] == $setData['capColorID'] ? 'selected' : '').' style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'">'.$color['colorDE'].'</option>';
                                            echo '
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Grundfarbe</td>
                                    <td>
                                        <select name="baseColorID" class="cel_100 cef_nomg cef_nopd" id="baseColor" onchange="InsertCapUpdateCapPreview()">
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                            ';
                                            foreach($colorList AS $color) echo '<option '.($color['id'] == $setData['baseColorID'] ? 'selected' : '').' style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'">'.$color['colorDE'].'</option>';
                                            echo '
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Textfarbe</td>
                                    <td>
                                        <select name="textColorID" class="cel_100 cef_nomg cef_nopd" id="textColor" onchange="InsertCapUpdateCapPreview()">
                                            <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                            ';
                                            foreach($colorList AS $color) echo '<option '.($color['id'] == $setData['textColorID'] ? 'selected' : '').' style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'">'.$color['colorDE'].'</option>';
                                            echo '
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Erhaltsort</td>
                                    <td><input class="cel_m" type="text" name="locationAquired" placeholder="Erhaltsort..." value="'.$setData['locationAquired'].'"/></td>
                                </tr>

                                <tr>
                                    <td>Erhaltsdatum</td>
                                    <td><input class="cel_m" type="text" name="dateAquired" placeholder="Erhaltsdatum..." value="'.$setData['dateAquired'].'"/></td>
                                </tr>

                                <tr>
                                    <td>Qualit&auml;t</td>
                                    <td>
                                        <select name="quality" class="cel_m cef_nomg cef_nopd" id="">
                                            <option value="" selected>--- Ausw&auml;hlen ---</option>
                                            ';
                                            foreach($qualityValueList AS $quality) echo '<option '.($quality == $setData['quality'] ? 'selected' : '').' value="'.$quality.'">'.$qualityDisplayList[$quality].'</option>';
                                            echo '
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Erhalten durch</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>'.RadioButton("Tausch","isTraded",$setData['isTraded'],"1").'</td>
                                                <td>'.RadioButton("Kauf","isTraded",!$setData['isTraded'],"0").'</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Zustand</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>'.RadioButton("Neu","isUsed",!$setData['isUsed'],"0","InsertCapUpdateCapPreview()").'</td>
                                                <td>'.RadioButton("Gebr.","isUsed",$setData['isUsed'],"1","InsertCapUpdateCapPreview()").'</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Tauschbar</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>'.RadioButton("Ja","isTradeable",$setData['isTradeable'],"1").'</td>
                                                <td>'.RadioButton("Nein","isTradeable",!$setData['isTradeable'],"0").'</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Drehverschluss</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>'.RadioButton("Ja","isTwistLock",$setData['isTwistlock'],"1","InsertCapUpdateCapPreview()").'</td>
                                                <td>'.RadioButton("Nein","isTwistLock",!$setData['isTwistlock'],"0","InsertCapUpdateCapPreview()").'</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>



                                <tr>
                                    <td colspan=2>
                                        <br>
                                        <button type="submit" name="editSetDetails" value="'.$_GET['objID'].'">Set-Daten aktualisieren</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </center>
                ';
            }
        }

        if($_GET['section'] == "set-konfigurieren")
        {
            $setData = MySQL::Row("SELECT * FROM sets WHERE setFilepath = ?",'s',$_GET['set']);


            $setDetailInfo = explode(';;',$setData['setTmpData']);
            $capData = array();
            for($i = 0 ; $i < count($setDetailInfo) ; $i ++)
            {
                $cd = explode('=',$setDetailInfo[$i]);
                $capData[str_replace(' ','',$cd[0])] = $cd[1];
            }

            $breweryData = MySQL::Row("SELECT * FROM breweries WHERE id = ?",'i',$capData['flavorID']);
            $countryData = MySQL::Row("SELECT * FROM countries WHERE id = ?",'i',$breweryData['countryID']);
            $colorList = MySQL::Cluster("SELECT * FROM colors");

            echo '<h2>Set hinzuf&uuml;gen</h2>';

            echo '
                <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <input type="hidden" id="selectedCapImage"/>
            ';

            echo '
                <center>
                <iframe src="/_iframe_setImageExtraUpload?set='.$_GET['set'].'&country='.$countryData['countryShort'].'" frameborder="0" style="height: 140px;" scrolling="no"></iframe><br><br>
            ';
            for($i = 0 ; $i < $setData['setSize'] ; $i++)
            {
                echo '
                    <input type="" name="capImage'.$i.'" id="capImage'.$i.'" hidden/>
                    <table class="addSet2Table">
                        <tr>
                            <td>
                                <img src="/content/not_found.png" alt="" id="setImage'.$i.'"/>
                                <a href="#capImgSelect"><button type="button" onclick="SetCapImageIDSet('.$i.');">Foto w&auml;hlen</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" name="capNumber'.$i.'" class="cel_100" placeholder="Kapsel-Nummer..." value="'.$capData['capNumberBase'].'_"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" name="capName'.$i.'" class="cel_100" placeholder="Name / Info..."/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="number" name="stock'.$i.'" class="cel_100" placeholder="Auf Lager"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <center>
                                <select name="capColor'.$i.'" id="" class="cel_30 cef_nomg">
                                    <option value="">Kapsel</option>
                                    ';
                                        foreach($colorList AS $color) echo '<option '.(($capData['capColorID'] == $color['id']) ? 'selected' : '').' style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'">'.$color['colorDE'].'</option>';
                                    echo '
                                </select>
                                <select name="baseColor'.$i.'" id="" class="cel_30 cef_nomg">
                                    <option value="">Grund</option>
                                    ';
                                        foreach($colorList AS $color) echo '<option '.(($capData['baseColorID'] == $color['id']) ? 'selected' : '').' style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'">'.$color['colorDE'].'</option>';
                                    echo '
                                </select>
                                <select name="textColor'.$i.'" id="" class="cel_30 cef_nomg">
                                    <option value="">Text</option>
                                    ';
                                        foreach($colorList AS $color) echo '<option '.(($capData['textColorID'] == $color['id']) ? 'selected' : '').' style="background:#'.$color['hex'].'; color: #'.($color['hex']=="FFFFFF" ? "000000" : "FFFFFF").';" value="'.$color['id'].'">'.$color['colorDE'].'</option>';
                                    echo '
                                </select>
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                '.Tickbox("isOwned$i","isOwned$i","&nbsp;In Besitz",true).'
                            </td>
                        </tr>
                    </table>
                ';
            }
            echo '
                        <br><br>
                        <button type="submit" value="'.$_GET['set'].'" name="addSetPart2" class="cel_l">Set eintragen</button>
                    </center>
                </form>
            ';


            echo '
                <div class="modal_wrapper" id="capImgSelect">
                    <a href="#c"><div class="modal_bg"></div></a>
                    <div class="modal_container" style="width: 50%; height: 60%; background: #2b2b2b; border-radius: 20px;">
                        <h3>Bild Ausw&auml;hlen</h3>
                        <iframe src="/_iframe_setImageSelector?set='.$setData['setFilepath'].'&country='.$countryData['countryShort'].'" frameborder="0" style="width: 100%; height: 100%;"></iframe>
                    </div>
                </div>
            ';

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
                                    <select name="countryID" id="countryID" class="cel_m cef_nomg" onchange="DynLoadExist(1,this,\'outCountryHasRegions\',\'SELECT * FROM regions WHERE countryID = ??\');">
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
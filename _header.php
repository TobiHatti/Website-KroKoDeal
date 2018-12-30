<?php
    session_start();
	require("_headerincludes.php");

    MySQL::PeriodicSave();

    if(isset($_POST['changeContent']))
    {
        $postParts = explode('||',$_POST['changeContent']);
        $page = $postParts[0];
        $pidx = $postParts[1];

        $content = $_POST['contentEdit'];

        if(!MySQL::Exist("SELECT id FROM pagecontents WHERE page = ? AND paragraphIndex = ?",'@s',$page,$pidx))
        {
            MySQL::NonQuery("INSERT INTO pagecontents (id, page, paragraphIndex) VALUES ('',?,?)",'@s',$page,$pidx);
        }

        MySQL::NonQuery("UPDATE pagecontents SET content = ? WHERE page = ? AND paragraphIndex = ?",'@s',$content,$page,$pidx);

        Page::Redirect(Page::This("!editContent"));
        die();
    }


    echo '
        <!DOCTYPE html>
        <html id="mainPage">
            <head>
    ';

    require("_headerlinks.php");

    echo '
            <script>
                window.addEventListener("scroll", function(e) {
                    var scrOffset = window.scrollY;

                    if(scrOffset > 100) document.getElementById("scrollNavBar").style.display = "block";
                    else document.getElementById("scrollNavBar").style.display = "none";
                });
            </script>

            </head>
            <body id="mainBody">
                <header>
                    <div style="text-align: right;" class="signInButtonContainer">
                        '.((isset($_SESSION['userID'])) ? ('Angemeldet als '.$_SESSION['userUsername'].' - <a href="/sign-out">Abmelden</a><br><br><a href="/tauschkorb"><i class="fas fa-shopping-cart"></i> Tausch-Korb</a>') : ('<a href="/sign-in">Anmelden</a>|<a href="/sign-up">Registrieren</a>')).'
                    </div>

                    <div id="scrollNavBar" class="scrollNavBar" style="display: none">
                        '.((isset($_SESSION['userID'])) ? ('<a href="/tauschkorb"><i class="fas fa-shopping-cart"></i> Tausch-Korb</a>') : '').'
                    </div>

                    <div class="cartAddNotification" id="cartAddNotification" style="display:none;">
                        Gegenstand zum Tausch-Korb hinzugef&uuml;gt!
                    </div>
                </header>
                <nav>
                    <div class="container">
                        <ul id="nav">
                            <li><a href="/">Home</a></li>
                            <li><a class="hsubs" href="#">Sammlung</a>
                                <ul class="subs">
                                    <li><a href="/laender">Kronkorken</a></li>
                                    <li><a href="/sets">Sets</a></li>
                                    <li><a href="/kronkorken/alle">Alle Kronkorken</a></li>
                                    <li><a href="#">Unbekannte Kronkorken</a></li>
                                </ul>
                            </li>
                            <li><a class="hsubs" href="#">Tauschen</a>
                                <ul class="subs">
                                    <li><a href="/tauschen/hilfe">Wie wird getauscht</a></li>
                                    <li><a href="/tauschen/laender">Kronkorken Tauschen</a></li>
                                    <li><a href="/tauschen/sets">Sets Tauschen</a></li>
                                </ul>
                            </li>
                            <li><a class="hsubs" href="#">Mehr</a>
                                <ul class="subs">
                                    <li><a href="/kontakt">Kontakt</a></li>
                                    <li><a href="/informationen">Infos</a></li>
                                    <li><a href="/gaestebuch">G&auml;stebuch</a></li>
                                    <li><a href="/forum">Forum</a></li>
                                    <li><a href="/brauereien">Brauereien</a></li>
                                </ul>
                            </li>
                            ';

                            if(isset($_SESSION['userID']) AND $_SESSION['userRank'] > 90)
                            {
                                echo '
                                    <li><a class="hsubs" href="#">Verwaltung</a>
                                        <ul class="subs">
                                            <li><a href="/eintragen/kronkorken">Kronkorken hinzuf&uuml;gen</a></li>
                                            <li><a href="/eintragen/set">Set hinzuf&uuml;gen</a></li>
                                            <li><a href="/eintragen/brauerei">Brauerei hinzuf&uuml;gen</a></li>
                                            <li><a href="/eintragen/sorte">Sorte hinzuf&uuml;gen</a></li>
                                            <li><a href="/eintragen/randzeichen">Randzeichen hinzuf&uuml;gen</a></li>
                                        </ul>
                                    </li>
                                ';
                            }

                            echo '
                            <div id="lavalamp"></div>

                            <form action="/suche" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                <div class="searchbar">
                                    <input type="search" class="cef_nomg" placeholder="Suche..." name="searchValue"/>
                                    <button type="submit" class="cef_nomg"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </ul>
                    </div>

                    <div class="navigationPath">
                        <a href=""><span>Home</span></a> <i>&gt;</i>
                    </div>
                </nav>
                <main>
    ';

    echo DynLoad::Start(1);
    echo DynLoad::Start(2);
    echo DynLoad::Start(3);
    echo DynLoad::Start(4);
    echo DynLoad::Start(5);

?>
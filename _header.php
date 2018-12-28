<?php
    session_start();
	require("_headerincludes.php");

    MySQL::PeriodicSave();

    echo '
        <!DOCTYPE html>
        <html id="mainPage">
            <head>
    ';

    require("_headerlinks.php");

    echo '
            </head>
            <body id="mainBody">
                <header>
                    <div style="text-align: right;" class="signInButtonContainer">
                        '.((isset($_SESSION['userID'])) ? ('Angemeldet als '.$_SESSION['userUsername'].' - <a href="/sign-out">Abmelden</a><br><br><a href="/einkaufswagen"><i class="fas fa-shopping-cart"></i> Einkaufswagen</a>') : ('<a href="/sign-in">Anmelden</a>|<a href="/sign-up">Registrieren</a>')).'
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
                                    <li><a href="#">Kontakt</a></li>
                                    <li><a href="#">Infos</a></li>
                                    <li><a href="#">G&auml;stebuch</a></li>
                                    <li><a href="#">Forum</a></li>
                                    <li><a href="#">Brauereien</a></li>
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
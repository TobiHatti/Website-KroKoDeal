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
                                    <li><a href="#">Wie wird getauscht</a></li>
                                    <li><a href="#">Kronkorken Tauschen</a></li>
                                    <li><a href="#">Sets Tauschen</a></li>
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
                            <div id="lavalamp"></div>

                            <form action="/suche" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                <div class="searchbar">
                                    <input type="search" placeholder="Suche..." name="searchValue"/>
                                    <button type="submit"><i class="fas fa-search"></i></button>
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

?>
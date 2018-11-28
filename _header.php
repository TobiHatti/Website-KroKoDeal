<?php
    session_start();
	require("_headerincludes.php");


    echo '
        <!DOCTYPE html>
        <html id="htmlfoundation">
            <head>
    ';

    require("_headerlinks.php");

    echo '
            </head>
            <body>
                <header>

                </header>
                <nav>
                    <div class="container">
                        <ul id="nav">
                            <li><a href="/">Home</a></li>
                            <li><a class="hsubs" href="#">Sammlung</a>
                                <ul class="subs">
                                    <li><a href="#">Kronkorken</a></li>
                                    <li><a href="#">Sets</a></li>
                                    <li><a href="#">Alle Kronkorken</a></li>
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

                            <div class="searchbar">
                                <input type="search" placeholder="Suche..."/>
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </ul>
                    </div>

                    <div class="navigationPath">
                        <a href=""><span>Home</span></a> <i>&gt;</i>
                    </div>
                </nav>
                <main>
    ';

?>
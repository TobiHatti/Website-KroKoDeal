<?php
	require("_header.php");

    if(isset($_GET['section']) AND isset($_SESSION['userID']) AND $_SESSION['userRank'] > 90)
    {
        if($_GET['section'] == 'kronkorken')
        {
            echo '<h2>Kronkorken hinzuf&uuml;gen</h2>';

            $mainSideSigns = "SELECT *,COUNT(sidesignID) AS sidesignCount FROM bottlecaps GROUP BY sidesignID HAVING sidesignCount >= 3";

            echo '
                <table class="addCapTable">
                    <tr>
                        <td colspan=3>Allgemeines</td>
                        <td rowspan=13></td>
                        <td colspan=3>Bilder</td>
                    </tr>

                    <tr>
                        <td>Land</td>
                        <td>
                            <select name="" class="cel_100 cef_nomg cef_nopd" id="">

                            </select>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>

                        <td rowspan=5><img src="" alt="" /></td>
                        <td rowspan=5 colspan=2><img src="" alt="" /></td>
                    </tr>

                    <tr>
                        <td>Brauerei</td>
                        <td><input class="cel_m" type="" /></td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                    <tr>
                        <td>Name</td>
                        <td><input class="cel_m" type="" /></td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                    <tr>
                        <td>Sorte</td>
                        <td>
                            <select name="" class="cel_100 cef_nomg cef_nopd" id="">

                            </select>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                    <tr>
                        <td>Kapsel-Nr</td>
                        <td><input class="cel_m" type="" /></td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                    <tr>
                        <td colspan=3>Zusatzinfos</td>
                        <td colspan=3>Optische angaben</td>
                    </tr>

                    <tr>
                        <td>Erhaltsort</td>
                        <td><input class="cel_m" type="" /></td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>

                        <td>Kapselfarbe</td>
                        <td>
                            <select name="" class="cel_100 cef_nomg cef_nopd" id="">

                            </select>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                    <tr>
                        <td>Erhaltsdatum</td>
                        <td><input class="cel_m" type="" /></td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>

                        <td>Grundfarbe</td>
                        <td>
                            <select name="" class="cel_100 cef_nomg cef_nopd" id="">

                            </select>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                    <tr>
                        <td>Qualit&auml;t</td>
                        <td>
                            <select name="" class="cel_100 cef_nomg cef_nopd" id="">

                            </select>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>

                        <td>Textfarbe</td>
                        <td>
                            <select name="" class="cel_100 cef_nomg cef_nopd" id="">

                            </select>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                    <tr>
                        <td>Getauscht</td>
                        <td>
                            <table>
                                <tr>
                                    <td>'.RadioButton("Ja","XXX").'</td>
                                    <td>'.RadioButton("Nein","XXX").'</td>
                                </tr>
                            </table>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>

                        <td>Zustand</td>
                        <td>
                            <table>
                                <tr>
                                    <td>'.RadioButton("Neu","XXX").'</td>
                                    <td>'.RadioButton("Gebr.","XXX").'</td>
                                </tr>
                            </table>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                    <tr>
                        <td>Tauschbar</td>
                        <td>
                            <table>
                                <tr>
                                    <td>'.RadioButton("Ja","XXX").'</td>
                                    <td>'.RadioButton("Nein","XXX").'</td>
                                </tr>
                            </table>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>

                        <td>Drehverschluss</td>
                        <td>
                            <table>
                                <tr>
                                    <td>'.RadioButton("Ja","XXX").'</td>
                                    <td>'.RadioButton("Nein","XXX").'</td>
                                </tr>
                            </table>
                        </td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                    <tr>
                        <td>Alkoholgehalt</td>
                        <td><input class="cel_m" type="" /></td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>

                        <td>Mitz&auml;hlen</td>
                        <td>'.Tickbox("count","count","",true).'</td>
                        <td>'.Tickbox("XXX","XXX","",true).'</td>
                    </tr>

                </table>
            ';
        }

        if($_GET['section'] == 'set')
        {
            echo '<h2>Set hinzuf&uuml;gen</h2>';
        }

        if($_GET['section'] == 'brauerei')
        {
            echo '<h2>Brauerei hinzuf&uuml;gen</h2>';
        }

        if($_GET['section'] == 'sorte')
        {
            echo '<h2>Sorte hinzuf&uuml;gen</h2>';
        }

        if($_GET['section'] == 'randzeichen')
        {
            echo '<h2>Randzeichen hinzuf&uuml;gen</h2>';
        }
    }
	
	include("_footer.php");
?>
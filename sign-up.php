<?php
	require("_header.php");

    if(isset($_SESSION['securePassageName']) AND isset($_POST['signin'.$_SESSION['securePassageName']]))
    {
        $gender = $_POST['gender'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $birthdate = $_POST['birthdateYear'].'-'.str_pad($_POST['birthdateMonth'], 2, '0', STR_PAD_LEFT).'-'.str_pad($_POST['birthdateDay'], 2, '0', STR_PAD_LEFT);
        $street = $_POST['street'];
        $streetnumber = $_POST['streetnumber'];
        $city = $_POST['city'];
        $zip = $_POST['zip'];
        $country = $_POST['country'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $notifications = isset($_POST['notifications']) ? 1 : 0;
        $joinedDate = date("Y-m-d");

        if($password != $cpassword) Page::Redirect(Page::This("-errorEmail","-errorUsername","-errorPassword","+errorPassword"));
        else if(MySQL::Exist("SELECT id FROM users WHERE username = ?",'s',$username)) Page::Redirect(Page::This("-errorEmail","-errorUsername","-errorPassword","+errorUsername"));
        else if(MySQL::Exist("SELECT id FROM users WHERE email = ?",'s',$email)) Page::Redirect(Page::This("-errorEmail","-errorUsername","-errorPassword","+errorEmail"));
        else
        {
            $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

            $sqlStatement = "INSERT INTO users
            (id,gender,firstName,lastName,username,email,password,birthdate,countryID,city,zipCode,street,streetNr,notifications,profileImage,joinedDate,bio,rank)
            VALUES
            ('',?,?,?,?,?,?,?,?,?,?,?,?,?,'',?,'','1')";
            MySQL::NonQuery($sqlStatement,'@s',$gender,$firstName,$lastName,$username,$email,$passwordHashed,$birthdate,$country,$city,$zip,$street,$streetnumber,$notifications,$joinedDate);


            Page::Redirect("/sign-in?accountCreated");
        }
        die();
    }

    $_SESSION['securePassageName'] = uniqid();

    echo '
        <head>

        </head>

        <h2>Registrieren</h2>

        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute("6LdGnH8UAAAAAJ8y1OFQ9fknUd-uZ5ewXQJixETO", {action: "homepage"})
                    .then(function(token) {
                    document.getElementById("submitButton").disabled = false;
                    document.getElementById("submitButton").name = "signin'.$_SESSION['securePassageName'].'";
                }
                );
            });
        </script>

        <center>
            '.(isset($_GET['errorPassword']) ? '<h4 style="color:red">Passw&ouml;rter nicht identisch!</h4>' : '').'
            '.(isset($_GET['errorUsername']) ? '<h4 style="color:red">Benutzername bereits vergeben!</h4>' : '').'
            '.(isset($_GET['errorEmail']) ? '<h4 style="color:red">E-Mail Adresse bereits registriert!</h4>' : '').'

            <input type="hidden" value="0" id="outUsernameExists"/>
            <input type="hidden" value="0" id="outEmailExists"/>

            <script>
                setInterval(function() {
                    ToggleElementVisibilityByElement("outUsernameExists","warningUsername","block");
                    ToggleElementVisibilityByElement("outEmailExists","warningEmail","block");

                    if(document.getElementById("outUsernameExists").value != 0 || document.getElementById("outEmailExists").value != 0) document.getElementById("submitButton").disabled = true;
                    else document.getElementById("submitButton").disabled = false;

                }, 100);
            </script>

            <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <table class="loginRegisterTable">
                    <tr>
                        <td>Anrede<i>*</i></td>
                        <td>
                            <table>
                                <tr>
                                    <td>'.RadioButton("Herr","gender",true,$value="M") .'</td>
                                    <td>'.RadioButton("Frau","gender",false,$value="F") .'</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>Vorname<i>*</i></td>
                        <td><input required type="text" class="cel_100" name="firstName" placeholder="Vorname..."/></td>
                    </tr>
                    <tr>
                        <td>Nachame<i>*</i></td>
                        <td><input required type="text" class="cel_100" name="lastName" placeholder="Nachname..."/></td>
                    </tr>

                    <tr><td><br></td></tr>

                    <tr>
                        <td>Benutzername<i>*</i></td>
                        <td>
                            <output id="warningUsername" style="color: #CC0000; display: none;">Benutzername bereits vergeben!</output>
                            <input required type="text" class="cel_100" name="username" placeholder="Benutzername..." oninput="DynLoadExist(this,\'outUsernameExists\',\'SELECT * FROM users WHERE username = ??\');"/>
                        </td>
                    </tr>


                    <tr>
                        <td>E-Mail Adresse<i>*</i></td>
                        <td>
                            <output id="warningEmail" style="color: #CC0000; display: none;">E-Mail bereits registriert!</output>
                            <input required type="email" class="cel_100" name="email" placeholder="E-Mail..." oninput="DynLoadExist(this,\'outEmailExists\',\'SELECT * FROM users WHERE email = ??\');"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Geburtsdatum<i>*</i></td>
                        <td>
                            <select name="birthdateDay" class="cel_xs cef_nomg">
                                ';
                                for($i=1;$i<=31;$i++) echo '<option value="'.$i.'">'.$i.'</option>';
                                echo '
                            </select>

                            <select name="birthdateMonth" class="cel_s cef_nomg">
                                <option value="01">J&auml;nner</option>
                                <option value="02">Februar</option>
                                <option value="03">M&auml;rz</option>
                                <option value="04">April</option>
                                <option value="05">Mai</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Dezember</option>
                            </select>

                            <select name="birthdateYear" class="cel_xs cef_nomg">
                                ';
                                for($i=date("Y");$i>(date("Y")-100);$i--) echo '<option value="'.$i.'">'.$i.'</option>';
                                echo '
                            </select>
                        </td>
                    </tr>

                    <tr><td><br></td></tr>

                    <tr>
                        <td>Stra&szlig;e / Nr</td>
                        <td>
                            <input type="text" class="cel_m" name="street" placeholder="Stra&szlig;e..."/>
                            <input type="text" class="cel_xs" name="streetnumber" placeholder="Nr..."/>
                        </td>
                    </tr>
                    <tr>
                        <td>Ort / PLZ</td>
                        <td>
                            <input type="text" class="cel_m" name="city" placeholder="Ort..."/>
                            <input type="text" class="cel_xs" name="zip" placeholder="PLZ..."/>
                        </td>
                    </tr>
                    <tr>
                        <td>Land<i>*</i></td>
                        <td>
                            <select required name="country" class="cel_100 cef_nomg">
                                ';
                                $countryList = MySQL::Cluster("SELECT * FROM countries ORDER BY countryDE ASC");
                                foreach($countryList AS $country) echo '<option value="'.$country['id'].'">'.$country['countryDE'].'</option>';
                                echo '
                            </select>
                        </td>
                    </tr>

                    <tr><td><br></td></tr>

                    <tr>
                        <td>Passwort<i>*</i></td>
                        <td><input type="password" class="cel_100" name="password" placeholder="Passwort..."/></td>
                    </tr>
                    <tr>
                        <td>Passwort wiederholen<i>*</i></td>
                        <td><input type="password" class="cel_100" name="cpassword" placeholder="Passwort..."/></td>
                    </tr>

                    <tr><td><br></td></tr>

                    <tr>
                        <td colspan=2>
                            <table>
                                <tr>
                                    <td>'.Tickbox("notifications","notifications","").'</td>
                                    <td style="text-align: left;">Ich m&ouml;chte wichtige Benachrichtigungen wie<br>Tauschangebote etc. per E-Mail erhalten</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr><td colspan=2><sub>Mit <i>*</i> markierte Felder sind Pflichtfelder</sub></td></tr>

                    <tr>
                        <td colspan=2>
                            <br>
                            <button type="submit" name="" id="submitButton" disabled>Registrieren</button>
                        </td>
                    </tr>
                </table>
            </form>
            <br><br>
        </center>
    ';
	
	include("_footer.php");
?>
<?php
	require("_header.php");

    if(isset($_POST['signin']))
    {
        $username = strtolower($_POST['username']);
        $password = $_POST['password'];

        $phash = MySQL::Scalar("SELECT password FROM users WHERE LOWER(username) = ?",'s',$username);

        if(password_verify($password,$phash))
        {
            $userData = MySQL::Row("SELECT * FROM users WHERE LOWER(username) = ?",'s',$username);

            $_SESSION['userID'] = $userData['id'];
            $_SESSION['userRank'] = $userData['rank'];
            $_SESSION['userFirstName'] = $userData['firstName'];
            $_SESSION['userLastName'] = $userData['lastName'];
            $_SESSION['userUsername'] = $userData['username'];

            Page::Redirect("/");
        }
        else Page::Redirect(Page::This("-error","-accountCreated","+error"));

        die();
    }

    echo '
        <h2>Anmelden</h2>

        <center>
            '.(isset($_GET['error']) ? '<h4 style="color:red">E-Mail oder Passwort nicht korrekt!</h4>' : '').'
            '.(isset($_GET['accountCreated']) ? '<h4 style="color:#32CD32">Account wurde angelegt!</h4>' : '').'
            <form action="'.Page::This().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <table class="loginRegisterTable">
                    <tr>
                        <td>Benutzername</td>
                        <td><input type="text" name="username" placeholder="Benutzername..." required/></td>
                    </tr>
                    <tr>
                        <td>Passwort</td>
                        <td><input type="password" name="password" placeholder="Passwort..." required/></td>
                    </tr>
                    <tr>
                        <td colspan=2>
                            <br>
                            <button type="submit" name="signin">Anmelden</button>
                        </td>
                    </tr>
                </table>
            </form>
            <br><br>
        </center>
    ';

	include("_footer.php");
?>
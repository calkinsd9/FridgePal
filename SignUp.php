<?php
session_start();

//redirect logged in users to their Inventory
if (isset($_SESSION['username'])){
    header("Location: Inventory.php");
}

?>

<html>
<head>
    <title>Sign Up</title>
    <style>
        .warning{
            display: none;
        }

    </style>
</head>
<body>
<h1>Sign Up</h1>
<form action="SignUp.php" method="post">
    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="" />
    <p class="warning" id="usernameWarning">* That username has already been taken; please choose another</p>
    <br>
    <label for="password">Password</label>
    <input type="text" name="password" id="password" value="" /> <br />
    <br>
    <br>
    <input type="submit" name="submitButton" value="Submit" />
</form>

</body>
<script type="text/javascript">
    var ajaxOnLoad = function () {
        var inputUsername = document.getElementById("username");
        var ajax = new XMLHttpRequest();
        ajax.open("GET", "./ajax/searchForUsername.php?username=" + inputUsername.value);
        ajax.onreadystatechange = function () {
            if (ajax.readyState === 4) {
                var response = ajax.responseText;
                console.log(response);
                if (response === "true") {
                    document.getElementById("usernameWarning").style.display = "";
                }
                else {
                    document.getElementById("usernameWarning").style.display = "none";
                }
            }
        };
        ajax.send();
    };
    window.onload = ajaxOnLoad;
</script>
</html>

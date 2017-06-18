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
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Michroma" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Sign Up</h1>
<form id="signup" action="Registration.php" method="post" onsubmit="event.preventDefault(); return validateForm();">
    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="" />
    <p class="warning" id="usernameWarning">* That username has already been taken; please choose another</p>
    <br>
    <label for="password">Password</label>
    <input type="text" name="password" id="password" value="" onblur="checkForEmpty()"/> <br />
    <p class="warning" id="passwordWarning">* Password cannot be blank</p>
    <br>
    <br>
    <input id="btnSubmit" type="submit" name="submitButton" value="Submit"/>
</form>

</body>
<script type="text/javascript">
    var inputUsername = document.getElementById("username");
    inputUsername.addEventListener("blur", function (){
        var ajax = new XMLHttpRequest();
        ajax.open("GET", "./ajax/searchForUsername.php?username=" + inputUsername.value);
        ajax.onreadystatechange = function () {
            if (ajax.readyState === 4) {
                var response = ajax.responseText;
                console.log(response);
                if (response === "true") {
                    document.getElementById("usernameWarning").style.display = "inherit";
                    document.getElementById("btnSubmit").disabled = true;
                }
                else {
                    document.getElementById("usernameWarning").style.display = "none";
                    document.getElementById("btnSubmit").disabled = false;
                }
            }
        };
        ajax.send();
    });
</script>
<script type="text/javascript">
    var checkForEmpty = function () {
        var input = document.getElementById("password").value;
        if (input === ""){
            document.getElementById("passwordWarning").style.display = "inherit";
            document.getElementById("btnSubmit").disabled = true;
        }
        else {
            document.getElementById("passwordWarning").style.display = "none";
            document.getElementById("btnSubmit").disabled = false;
        }
    };
    
    var validateForm = function () {
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;
        if (username === "" || password === ""){
            alert("username and password must not be blank");
            return false;
        }

        document.getElementById("signup").submit();

        return true;
    }
</script>
</html>

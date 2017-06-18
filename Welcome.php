<?php
session_start();

//redirect logged in users to their Inventory
if (isset($_SESSION['username'])){
    header("Location: Inventory.php");
}

?>

<html>
<head>
    <title>Welcome to FridgePal</title>
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Michroma" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!--
<h1 class="companyName">FridgePal</h1>
!-->
<img src="Logo2.png" width="600em">
<p class="tagline"><em>Save money. Reduce waste. Eat wonderful food.</em></p>
<br>
<br>
<button type="button" id="btnSignUp">Sign up</button>
<button type="button" id="btnLogIn">Log in</button>
<table id="iconTable">
    <tr>
        <td><img class="featureIcon" src="OK.png"><br>Keep track of your<br>perishable items</td>
        <td><img class="featureIcon" src="List.png"><br>Custom sort by location</td>
        <td><img class="featureIcon" src="Search.png"><br>Search for recipes</td>
    </tr>
</table>
</body>
<script type="text/javascript">
    document.getElementById("btnSignUp").onclick = function () {
        location.href = "SignUp.php";
    };
    document.getElementById("btnLogIn").onclick = function () {
        location.href = "LogIn.php";
    };
</script>
</html>

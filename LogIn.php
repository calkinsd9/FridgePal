<?php
session_start();

//redirect logged in users to their Inventory
if (isset($_SESSION['username'])){
    header("Location: Inventory.php");
}

?>

<html>
<head>
    <title>Log In</title>
</head>
<body>
<h1>Log In</h1>
<form action="SignUp.php" method="post">
    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="" /> <br />
    <label for="password">Password</label>
    <input type="text" name="password" id="password" value="" /> <br />
    <br>
    <br>
    <input type="submit" name="submitButton" value="Submit" />
</form>
</body>
</html>

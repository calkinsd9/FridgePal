<?php
session_start();

//redirect logged in users to their Inventory
if (!isset($_SESSION['username'])){
    header("Location: LogIn.php");
}

?>

<html>
<head>
    <title></title>
</head>
<body>

</body>
</html>

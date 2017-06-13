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
</head>
<body>
<h1 class="companyName">FridgePal</h1>
<p class="tagline">Save money. Reduce waste. Eat wonderful food.</p>
<button type="button">Sign up</button>
<button type="button">Log in</button>
<!-- images eventually going here -->
</body>
</html>

<?php
function connect()
{
    // Create a mysqli object connected to the database.
    $connection = new mysqli("cis.gvsu.edu", "calkinda", "calkinda");
    // Complain if the the connection fails.  (This would have to be more graceful
    // in a production environment)
    if (!$connection || $connection->connect_error) {
        die('Unable to connect to database [' . $connection->connect_error . ']');
    }
    if (!$connection->select_db("calkinda")) {
        die ("Unable to select database:  [" . $connection->error . "]");
    }
    return $connection;
}

session_start();

//redirect logged in users to their Inventory
if (isset($_SESSION['username'])){
    header("Location: Inventory.php");
}

if (isset($_POST['username'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $c = connect();
    $sql = $pdo->prepare('SELECT username FROM foodUserPass WHERE username = ? AND password = ?;');
    if ($sql->execute([$username, $password])) {
        $result = $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
    var_dump($result);

}

?>

<html>
<head>
    <title>Log In</title>
</head>
<body>
<h1>Log In</h1>
<form action="LogIn.php" method="post">
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

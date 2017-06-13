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

function initializePDO()
{
    $host = 'cis.gvsu.edu';
    $db = 'calkinda';
    $user = 'calkinda';
    $pass = 'calkinda';
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    return (new PDO($dsn, $user, $pass, $opt));
}

session_start();

//redirect logged in users to their Inventory
if (isset($_SESSION['username'])){
    header("Location: Inventory.php");
}

if (isset($_POST['username'])){
    $username = $_POST['username'];
    $password = $_POST['password'];


    $pdo = initializePDO();
    $statement = $pdo->prepare('SELECT username FROM foodUserPass WHERE username = ? AND password = ?;');
    $statement->execute([$username, $password]);
    $result = $statement->fetch();
    //if the username password is wrong, $result will be a bool(false). if not, it will be an array with key 'username' that has value of the actual username

    if ($result){
        echo "Your username is " . $result['username'];
    }
    
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

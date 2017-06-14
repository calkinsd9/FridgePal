<?php
session_start();

//redirect logged in users to their Inventory
if (isset($_SESSION['username'])){
    header("Location: Inventory.php");
}
else {
    if ($_POST['username'] === null){
        header("Location: Welcome.php");
    }
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

?>
<html>
<head>
    <title>Registration</title>
</head>
<body>
<?php

$username = $_POST['username'];
$password = $_POST['password'];

$pdo = initializePDO();
$statement = $pdo->prepare('SELECT username FROM foodUserPass WHERE username = ?;');
$statement->execute([$username]);
$result = $statement->fetch();
if ($result !== false){
    echo <<<html
<p>That username has already been taken.</p>
<p>Please <a href="SignUp.php">click here</a> to try a different username.</p>
html;
}
else {
    $statement = $pdo->prepare('INSERT INTO foodUserPass (username, password, isAdmin) VALUES(?, ?, 0);');
    $statement->execute([$username, $password]);
    $_SESSION['username'] = $username;
    echo "<p>Congratulations $username!</p>";
    echo <<<html
    <p>You have successfully registered as a new member of FridgePal.</p>
    <p>Please <a href="AddItems.php">click here</a> to begin adding items to your inventory.</p>    
html;

}

?>

</body>
</html>

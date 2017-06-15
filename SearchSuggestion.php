<?php
session_start();

//redirect logged in users to their Inventory
if (!isset($_SESSION['username'])){
    header("Location: LogIn.php");
}

if (!key_exists("name", $_GET)) {
    header("Location: Inventory.php");
}

function initializePDO() {
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

$name = $_GET['name'];

$pdo = initializePDO();
$statement = $pdo->prepare('SELECT type, location, spoilDays FROM foodSearchSuggestions WHERE name = ?;');
$statement->execute([$name]);
$result = $statement->fetch();
if ($result === false){
    echo "";
}
else {
    $type = $result["type"];
    $location = $result["location"];
    $spoilDays = $result["spoilDays"];
    echo "type=$type;location=$location;spoilDays=$spoilDays";
}
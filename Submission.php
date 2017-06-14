<?php
session_start();

//redirect logged in users to their Inventory
if (!isset($_SESSION['username'])){
    header("Location: LogIn.php");
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

if (key_exists("food", $_POST)) {
    $username = $_SESSION['username'];
    $name = $_POST["name"];
    $type = $_POST["type"];
    $location = $_POST["location"];
    $spoilDays = $_POST["spoil"];
    $today = strtotime("today");
    $spoilDate = date("Y-m-d", strtotime("+$spoilDays days", $today));

    $pdo = initializePDO();
    $statement = $pdo->prepare('INSERT INTO foodMainStorage (name, type, location, spoilDate, createdDate, createdBy) VALUES(?, ?, ?, ?, ?, ?);');
    $statement->execute([$name, $type, $location, $spoilDate, $today, $username]);

    $statement = $pdo->prepare('SELECT id FROM foodMainStorage WHERE name = ? AND type = ? AND location = ? AND spoilDate = ? AND createdDate = ? AND createdBy = ?;');
    $statement->execute([$name, $type, $location, $spoilDate, $today, $username]);
    $result = $statement->fetch();
    $id = $result['id'];
    header("Location: Display.php?newItem=$id");
}
else {
    header("Location: AddItems.php");
}
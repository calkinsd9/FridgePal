<?php
//redirect logged in users to their Inventory
if (!isset($_SESSION['username'])){
    header("Location: LogIn.php");
}

if (!key_exists("id", $_GET)) {
    header("Location: Inventory.php");
}

//id=" + id + "&name=" + name + "&type=" + type + "&spoilDays=" + spoilDays + "&location=" + location);

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

$id = $_GET['id'];
$name = $_GET['name'];
$type = $_GET['type'];
$spoilDays = $_GET['spoilDays'];
$location = $_GET['location'];

//convert spoil days into a spoil date
$today = strtotime("today");
$spoilDate = date("Y-m-d", strtotime("+$spoilDays days", $today));

$pdo = initializePDO();
$statement = $pdo->prepare('UPDATE foodMainStorage SET name = ?, type = ?, location = ?, spoilDate = ? WHERE id = ?;');
$statement->execute([$name, $type, $location, $spoilDate, $id]);

//awkward bit of error checking
$statement = $pdo->prepare('SELECT id FROM foodMainStorage WHERE id = ? AND name = ? AND type = ? AND location = ? AND spoilDate = ?;');
$statement->execute([$id, $name, $type, $location, $spoilDate]);
$result = $statement->fetchAll();
echo count($result) > 0;
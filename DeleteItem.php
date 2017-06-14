<?php
session_start();

//redirect logged in users to their Inventory
if (!isset($_SESSION['username'])){
    header("Location: LogIn.php");
}

if (!key_exists("name", $_POST)) {
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

$pdo = initializePDO();
$id = $_POST['id'];
//DELETE FROM foodMainStorage WHERE id = '$id';"
$statement = $pdo->prepare('DELETE FROM foodMainStorage WHERE id = ?;');
$statement->execute([$id]);
echo "deleted";
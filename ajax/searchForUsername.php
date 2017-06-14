<?php
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
};

$username = $_GET['username'];
$pdo = initializePDO();
$statement = $pdo->prepare('SELECT username FROM foodUserPass WHERE username = ?;');
$statement->execute([$username]);
$result = $statement->fetch();
if ($result === false){
    echo "false";
}
else {
    echo "true";
}

?>
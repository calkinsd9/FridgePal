<?php
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

?>

<html>
<head>
    <title>Log In</title>
</head>
<body>
<h1>Log In</h1>

<?php
if (isset($_POST['username'])){
    $username = $_POST['username'];
    $password = $_POST['password'];


    $pdo = initializePDO();
    $statement = $pdo->prepare('SELECT username FROM foodUserPass WHERE username = ? AND password = ?;');
    $statement->execute([$username, $password]);
    $result = $statement->fetch();
    //incorrect login makes $result === false
    //correct login makes $result['username'] === user's login name
    if ($result){
        $_SESSION['username'] = $result;
        header("Location: Inventory.php");
    }
    else {
        echo "<P>That username and password are incorrect; please try again</p>";
    }

}

?>


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

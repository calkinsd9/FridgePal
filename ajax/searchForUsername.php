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

$c = connect();
$username = $_GET['username'];
$sql = "SELECT username FROM foodUserPass WHERE username = '$username';";
$result = $c->query($sql);
if (!$result) {
    echo "<p>Can't get any results.</p>";
    echo "$c->errno ; $c->error";
}
else {
    if ($result->num_rows > 0){
        echo "true";
    }
    else {
        echo "false";
    }
}
?>

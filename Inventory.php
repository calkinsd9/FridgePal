<?php
session_start();

//redirect logged in users to their Inventory
if (!isset($_SESSION['username'])){
    header("Location: LogIn.php");
}

?>

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

function getItems($location) {
    $username = $_SESSION['username'];
    $pdo = initializePDO();
    $statement = $pdo->prepare('SELECT id, name, type, spoilDate FROM foodMainStorage WHERE createdBy = ? AND location = ?;');
    $statement->execute([$username, $location]);
    $result = $statement->fetchAll();

    return $result;
}

function printTable($location){
    $location_lowercase = strtolower($location);
    echo <<<html
    <h2>$location:</h2>
    <table>
        <tr>
            <th>Ingredient</th>
            <th>Food</th>
            <th>Type</th>
            <th>Spoils in</th>
            <th></th>
        </tr>
html;

    $result = getItems($location_lowercase);
    $rowNum = 0;

    foreach ($result as $row) {
        //displayed row
        $foodID = $row['id'];
        echo "<tr id=\"$foodID\">";
        $name = $row['name'];
        echo "<td><input class='checkboxes' type=\"checkbox\" name=\"\" value=\"$name\"></td>";
        $keys = array("name", "type", "spoilDate");
        // iterate over all the columns.  Each column is a <td> element.
        foreach ($keys as $key) {
            if ($key === "spoilDate"){
                $today = new DateTime("now");
                $spoilDate = date_create($row[$key]);
                $interval = date_diff($today, $spoilDate);
                $interval = intval($interval->format('%r%a'));
                echo "<td class='$key'>$interval</td>";
            }
            else {
                echo "<td class='$key'>" . $row[$key] . "</td>";
            }
        }
        echo "<td> <button class='editButton' id='btnEdit$foodID' onclick=\"editItem()\">Edit</button> </td>";
        echo "<td><button class='deleteButton' id='btnDelete$foodID'>Delete</button></td>";
        echo "</tr>\n";

        //hidden form for editing
        echo "<tr id='edit$foodID' class='editRow'>";
        echo "<td></td>";
        foreach ($keys as $key) {
            if ($key === "spoilDate"){
                echo "<td><input type='text' name='spoilDays' value='$interval'>";
            }
            else {
                echo "<td><input type='text' name='$key' value='$row[$key]'></td>";
            }
        }
        echo "<td><button class='doneButton' id='btnDone$foodID' onclick=\"saveEdit()\">Done</button></td>";
        $rowNum += 1;
    }
    echo "</table><br />";
}

?>

<html>
<head>
    <title>Your Inventory</title>
    <style>
        .editRow{
            display: none;
        }
    </style>
</head>
<body>
<h1>Current Inventory:</h1>
<br/>

<?php
printTable("Fridge");
printTable("Freezer");
printTable("Pantry");
?>

<button id="btnRecipeSearch" onclick="recipeSearch()">Search AllRecipes</button>
<script type="text/javascript">
    var editItem = function() {
        //hide everything in this's row
        var row = this.parentNode.parentNode;
        row.style.display = 'none';

        //get the foodID
        var foodID = row.id;

        //get the hidden form that corresponds to that foodID
        var editRow = document.getElementById('edit' + foodID);

        //make it visible
        editRow.style.display = 'inherit';
    };
</script>
<script type="text/javascript">
    var ajaxOnLoad = function () {
        //attach event listeners to all Delete buttons and bind them to ajax calls
        var deleteButtons = document.getElementsByClassName('deleteButton');
        for (var i = 0; i < deleteButtons.length; i++) {
            deleteButtons[i].addEventListener("click", function () {
                var row = deleteButtons[i].parentNode.parentNode;
                var deleteItemName = row.getElementsByClassName("name")[0].value;
                if (confirm("Are you sure that you want to remove " + deleteItemName + " from your inventory?")) {
                    var id = row.id;
                    var ajax = new XMLHttpRequest();
                    ajax.open("GET", "DeleteItem.php?id=" + id);
                    ajax.onreadystatechange = function () {
                        if (ajax.readyState === 4) {
                            if (ajax.responseText === "deleted") {
                                //remove the original row
                                var table = row.parentNode;
                                table.removeChild(row);
                            }
                            else {
                                alert(ajax.responseText);
                            }
                        }
                    };
                    ajax.send();
                }
            });
        }
    };
    window.onload = ajaxOnLoad;
</script>
<br />
<br />
<br />
<p>You are currently logged in as <?php echo $_SESSION['username']?>.</p>
<a href="./AddItems.php" >Add Items</a>
<a href="./Login.php?logout=true" >Click here to log out</a>
</body>
</html>

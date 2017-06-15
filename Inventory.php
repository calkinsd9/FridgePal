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
    <table id="$location_lowercase">
        <thead>
            <tr>
                <th>Ingredient</th>
                <th class="tableHeader">Food</th>
                <th class="tableHeader">Type</th>
                <th class="tableHeader">Spoils in</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="tbody_$location_lowercase">
html;

    $result = getItems($location_lowercase);

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
        echo "<td> <button class='editButton' id='btnEdit$foodID' onclick=\"editItem(this)\">Edit</button> </td>";
        echo "<td><button class='deleteButton' id='btnDelete$foodID'>Delete</button></td>";
        echo "</tr>\n";

        //hidden form for editing
        echo "<tr id='edit$foodID' class='editRow'>";
        echo "<td></td>";
        foreach ($keys as $key) {
            if ($key === "spoilDate"){
                echo "<td><input id='edit$key$foodID' type='text' name='spoilDate' value='$interval'>";
            }
            else {
                echo "<td><input id='edit$key$foodID' type='text' name='$key' value='$row[$key]'></td>";
            }
        }

        echo "<td><select id=\"locationSelect$foodID\" name=\"location\">";
        $options = array("Fridge", "Freezer", "Pantry");
        foreach ($options as $option){
            if ($option === $location){
                echo "<option value=\"" . strtolower($option) . "\" selected=\"selected\">" . $option . "</option>";
            }
            else {
                echo "<option value=\"" . strtolower($option) . "\">" . $option . "</option>";
            }
        }
        echo "</select></td>";

        echo "<td><button class='doneButton' id='btnDone$foodID' >Done</button></td>";

    }
    echo "</tbody></table><br />";
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
    var editItem = function(context) {
        //hide everything in this's row
        var row = context.parentNode.parentNode;
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
        var deleteButtons = document.getElementsByClassName("deleteButton");
        for (var i = 0; i < deleteButtons.length; i++) {
            deleteButtons[i].addEventListener("click", function () {
                var row = this.parentNode.parentNode;
                var deleteItemName = row.getElementsByClassName("name")[0].innerHTML;
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

        var doneButtons = document.getElementsByClassName('doneButton');
        for (i = 0; i < doneButtons.length; i++) {
            doneButtons[i].addEventListener("click", function () {
                var row = this.parentNode.parentNode;
                var table = row.parentNode.parentNode;
                var id = row.id.substr(4);
                var name = document.getElementById("editname" + id).value;
                var type = document.getElementById("edittype" + id).value;
                var spoilDays = document.getElementById("editspoilDate" + id).value;
                var locationSelect = document.getElementById("locationSelect" + id);
                var location = locationSelect.options[locationSelect.selectedIndex].value;

                //form validation
                if(name === "" || type === "" || isNaN(parseInt(spoilDays))) {
                    alert("You must include a name, type, and number of days till spoiled");
                }
                else {
                    //submit query
                    var ajax = new XMLHttpRequest();
                    ajax.open("GET", "ModifyItem.php?id=" + id + "&name=" + name + "&type=" + type + "&spoilDays=" + spoilDays + "&location=" + location);
                    ajax.onreadystatechange = function () {
                        if (ajax.readyState === 4) {
                            if (ajax.responseText === "true") {
                                //change the original values, except location
                                var displayRow = document.getElementById(id);
                                displayRow.getElementsByClassName("name")[0].innerHTML = name;
                                displayRow.getElementsByClassName("type")[0].innerHTML = type;
                                displayRow.getElementsByClassName("spoilDate")[0].innerHTML = spoilDays;

                                //if location changed, move the row and its edit row
                                var originalLocation = displayRow.parentNode.parentNode.id;
                                if (originalLocation !== location){
                                    var editRow = row.cloneNode(true);
                                    var newDisplayRow = displayRow.cloneNode(true);

                                    //add to new table
                                    document.getElementById("tbody_" + location).appendChild(newDisplayRow);
                                    document.getElementById("tbody_" + location).appendChild(editRow);

                                    //switch visibility
                                    editRow.style.display = "none";
                                    newDisplayRow.style.display = "inherit";

                                    //remove from old table
                                    row.parentNode.removeChild(row);
                                    displayRow.parentNode.removeChild(displayRow);
                                }
                                else{
                                    //switch visibility
                                    displayRow.style.display = "inherit";
                                    row.style.display = "none";
                                }
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
<script type="text/javascript">
    //attach sort method to columns
    var tableHeaders = document.getElementsByClassName("tableHeader");
    for (var i = 0; i < tableHeaders.length; i++){
        tableHeaders[i].onclick = function () {
            var headerName = this.innerHTML;
            //TODO: find out how to get the position of the header in the row
            var allHeaders = this.parentNode.getElementsByTagName('th');
            for (var k = 0; k < allHeaders.length; k++){
                if (allHeaders[k].innerHTML === headerName){
                    break;
                }
            }
            var headerPosition = k;
            var table = this.parentNode.parentNode.parentNode;
            var somethingMoved = true;
            do {
                somethingMoved = false;
                var rows = table.getElementsByTagName('tr');
                for (var j = 1; j < (rows.length - 2); j++){
                    var valueAbove = rows[j].getElementsByTagName('td')[headerPosition].innerHTML.toLowerCase();
                    var valueBelow = rows[j+2].getElementsByTagName('td')[headerPosition].innerHTML.toLowerCase();

                    if (valueAbove > valueBelow){
                        rows[j].parentNode.insertBefore(rows[j+2], rows[j]);
                        rows[j].parentNode.insertBefore(rows[j+2], rows[j+1]);
                        somethingMoved = true;
                        break;
                    }
                }
            } while (somethingMoved);
        }
    }

</script>
<br />
<br />
<br />
<p>You are currently logged in as <?php echo $_SESSION['username']?>.</p>
<a href="./AddItems.php" >Add Items</a>
<a href="./Login.php?logout=true" >Click here to log out</a>
</body>
</html>

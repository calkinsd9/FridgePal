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
        echo "<td id='checkbox$foodID'><input class='checkboxes' type=\"checkbox\" name=\"\" value=\"$name\"></td>";
        $keys = array("name", "type", "spoilDate");
        // iterate over all the columns.  Each column is a <td> element.
        foreach ($keys as $key) {
            if ($key === "spoilDate"){
                $today = new DateTime("now");
                $spoilDate = date_create($row[$key]);
                $interval = date_diff($today, $spoilDate);
                $interval = intval($interval->format('%r%a'));
                echo "<td class='$key' id='$key$foodID'>$interval</td>";
            }
            else {
                echo "<td class='$key' id='$key$foodID'>" . $row[$key] . "</td>";
            }
        }

        //edit, delete, and save buttons
        echo "<td>";
        echo "<input type=\"button\" id=\"btnEdit$foodID\" value=\"Edit\" class=\"edit\" onclick=\"editItem('$foodID')\">";
        echo "<input type=\"button\" id=\"btnSave$foodID\" value=\"Save\" class=\"save\" onclick=\"saveItem('$foodID')\">";
        echo "<input type=\"button\" id=\"btnDelete$foodID\" value=\"Delete\" class=\"delete\" onclick=\"deleteItem('$foodID')\">";
        echo "</td>";
        echo "</tr>";
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
        table, th, td {
            border: 1px solid black;
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
    var editItem = function(id) {
        //hide the edit button
        document.getElementById("btnEdit" + id).style.display = "none";

        //reveal the save button
        document.getElementById("btnSave" + id).style.display = "inline";

        //get the current data
        var checkbox = document.getElementById("checkbox" + id);
        var name = document.getElementById("name" + id);
        var type = document.getElementById("type" + id);
        var spoilDays = document.getElementById("spoilDate" + id);

        var nameData = name.innerHTML;
        var typeData = type.innerHTML;
        var spoilDaysData = spoilDays.innerHTML;
        var location = name.parentNode.parentNode.parentNode.id;

        //change the innerHTML on the old cells to be inputs
        var selectBoxHTML = "<select id='locationSelect" + id + "' name='" + location + "'>";
        var options = ['Fridge', 'Freezer', 'Pantry'];
        for (var i = 0; i < options.length; i++){
            if (options[i].toLowerCase() === location){
                //<option value=\"" . strtolower($option) . "\" selected=\"selected\">" . $option . "</option>"
                selectBoxHTML += "<option value='" + options[i].toLowerCase() + "' selected='selected'>" + options[i] + "</option>";
            }
            else {
                selectBoxHTML += "<option value='" + options[i].toLowerCase() + "'>" + options[i] + "</option>";
            }
        }
        selectBoxHTML += "</select>";

        checkbox.innerHTML = selectBoxHTML;
        name.innerHTML = "<input type='text' id='inputName" + id + "' value='" + nameData + "'>";
        type.innerHTML = "<input type='text' id='inputType" + id + "' value='" + typeData + "'>";
        spoilDays.innerHTML = "<input type='text' id='inputSpoilDays" + id + "' value='" + spoilDaysData + "'>";
    };
    
    var saveItem = function (id) {
        //get modified data
        var nameData = document.getElementById("inputName" + id).value;
        var typeData = document.getElementById("inputType" + id).value;
        var spoilDaysData = document.getElementById("inputSpoilDays" + id).value;
        var locationSelect = document.getElementById("locationSelect" + id);
        var locationData = locationSelect.options[locationSelect.selectedIndex].value;

        //validate data
        if (nameData === "" || typeData === "" || isNaN(parseInt(spoilDaysData))) {
            alert("You must include a name, type, and number of days till spoiled");
            return;
        }

        //if valid, save data using ajax call
        var ajax = new XMLHttpRequest();
        ajax.open("GET", "ModifyItem.php?id=" + id + "&name=" + nameData + "&type=" + typeData + "&spoilDays=" + spoilDaysData + "&location=" + locationData);
        ajax.onreadystatechange = function () {
            if (ajax.readyState === 4){
                if (ajax.responseText === "true") {
                    //change the row back to a display row with the new values
                    var checkbox = document.getElementById("checkbox" + id);
                    var name = document.getElementById("name" + id);
                    var type = document.getElementById("type" + id);
                    var spoilDays = document.getElementById("spoilDate" + id);

                    checkbox.innerHTML = "<input id='checkbox" + id + "' class='checkboxes' type='checkbox' name='' value='" + nameData + "'>";
                    name.innerHTML = nameData;
                    type.innerHTML = typeData;
                    spoilDays.innerHTML = spoilDaysData;

                    //hide the save button
                    document.getElementById("btnSave" + id).style.display = "none";

                    //reveal the edit button
                    document.getElementById("btnEdit" + id).style.display = "inline";

                    //if location changed, move the row to new table
                    var originalLocation = name.parentNode.parentNode.parentNode.id;
                    if (locationData !== originalLocation){
                        var destinationTable = document.getElementById(locationData);
                        destinationTable.getElementsByTagName("tbody")[0].appendChild(document.getElementById(id));
                    }
                }
                else {
                    alert(ajax.responseText);
                }
            }
        };
        ajax.send();
    };

    var deleteItem = function (id) {
        var name = document.getElementById("name" + id).innerHTML;
        if (confirm("Are you sure that you want to remove " + name + " from your inventory?")) {
            var ajax = new XMLHttpRequest();
            ajax.open("GET", "DeleteItem.php?id=" + id);
            ajax.onreadystatechange = function () {
                if (ajax.readyState === 4) {
                    if (ajax.responseText === "deleted") {
                        //remove the original row
                        var row = document.getElementById(id);
                        row.parentNode.removeChild(row);
                    }
                    else {
                        alert(ajax.responseText);
                    }
                }
            };
            ajax.send();
        }

    }
</script>
<script type="text/javascript">
    //attach sort method to columns
    var tableHeaders = document.getElementsByClassName("tableHeader");
    for (var i = 0; i < tableHeaders.length; i++){
        tableHeaders[i].onclick = function () {
            var headerName = this.innerHTML;
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

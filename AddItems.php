<?php
session_start();

//redirect logged in users to their Inventory
if (!isset($_SESSION['username'])){
    header("Location: LogIn.php");
}

?>

<html>
<head>
    <title>Add Items</title>
</head>
<body>
<h1>Add Item</h1>
<div>
    <form action="Submission.php" method="post" id="theForm" onsubmit="event.preventDefault(); return validateForm();" >
        <label for="nameInput">Name: </label>
        <input id="nameInput" type="text" name="name" value="" />
        <p class="warning" id="nameWarning" >* required</p>
        <br />
        <label for="typeInput">Type: </label>
        <input id="typeInput" type="text" name="type" value="" />
        <p class="warning" id="typeWarning" >* required</p>
        <br />
        <label for="locationSelect">Location: </label>
        <select id="locationSelect" name="location">
            <option value="fridge">Fridge</option>
            <option value="freezer">Freezer</option>
            <option value="pantry">Pantry</option>
        </select>
        <br />
        <label for="spoilInput">Days Till Spoiled: </label>
        <input id="spoilInput" type="number" name="spoil" value="" />
        <p class="warning" id="spoilWarning">* must be a valid number</p>

        <input id="submitButton" type="submit" name="submitButton" value="Submit" />
        <br />
    </form>
</div>

<p>You are currently logged in as <?php echo $_SESSION['username']?>.</p>
<a href="Logout.php">Click here to log out</a>

<script type="text/javascript">

    function validateForm() {
        var inputIsValid = true;
        var nameInput = document.getElementById('nameInput');
        var typeInput = document.getElementById('typeInput');
        var spoilInput = document.getElementById('spoilInput');

        //name cannot be blank
        if (nameInput.value === ""){
            nameInput.className = "invalid";
            document.getElementById("nameWarning").style.display = "initial";
            inputIsValid = false;
        }
        else {
            nameInput.className = "";
            document.getElementById("nameWarning").style.display = "none";
        }

        //type cannot be blank
        if (typeInput.value === ""){
            typeInput.className = "invalid";
            document.getElementById("typeWarning").style.display = "initial";
            inputIsValid = false;
        }
        else {
            typeInput.className = "";
            document.getElementById("typeWarning").style.display = "none";
        }

        //spoilDays must be a number
        if (isNaN(parseInt(spoilInput.value))){
            spoilInput.className = "invalid";
            document.getElementById("spoilWarning").style.display = "initial";
            inputIsValid = false;
        }
        else {
            spoilInput.className = "";
            document.getElementById("spoilWarning").style.display = "none";
        }


        if (inputIsValid){
            document.getElementById("theForm").submit();
        }

        return inputIsValid;
    }

</script>
<script type="text/javascript">
    var ajaxOnLoad = function () {
        var nameInput = document.getElementById('nameInput');
        nameInput.addEventListener("blur", function () {
            var ajax = new XMLHttpRequest();
            ajax.open("GET", "SearchSuggestion.php?name=" + nameInput.value);
            ajax.onreadystatechange = function () {
                if (ajax.readyState === 4) {
                    var theForm = document.getElementById("theForm");
                    var response = ajax.responseText;
                    if (response !== ""){

                        //get variables from ajax response
                        var responseArray = response.split(";");
                        var responseObject= {};
                        for (var i = 0; i < responseArray.length; i++) {
                            var split = responseArray[i].split('=');
                            responseObject[split[0].trim()] = split[1].trim();
                        }

                        document.getElementById("typeInput").value = responseObject.type;
                        switch (responseObject.location){
                            case "fridge":
                                document.getElementById("locationSelect").selectedIndex = "0";
                                break;
                            case "freezer":
                                document.getElementById("locationSelect").selectedIndex = "1";
                                break;
                            case "pantry":
                                document.getElementById("locationSelect").selectedIndex = "2";
                                break;
                        }
                        document.getElementById("spoilInput").value = responseObject.spoilDays;
                    }
                }
            };
            ajax.send();

        });
    };
    window.onload = ajaxOnLoad;
</script>
</body>
</html>

<?php

include('lib/common.php');
// written by carol cheung ccheung39@gatech.edu

if (!isset($_SESSION['email'])) {
    header('Location: add_tool.php');
    exit();
}
include('lib/show_queries.php');

//access most recent added_tool via $_SESSION assignment in add_tool.php
$tool_id = $_SESSION['tool_id'];

if( $_SERVER['REQUEST_METHOD'] == 'POST') {

/*    foreach ($_POST as $key => $value) {
        array_push($query_msg, "posting keys: " . $key);
        array_push($query_msg, "posting value: " . $value);
    }*/

    $handle = mysqli_real_escape_string($db, $_POST['handle']);

    include('./add_tool_queryGarden.php');

    $bladeL = mysqli_real_escape_string($db, $_POST['bladeLength']);
    $bladeLfraction = mysqli_real_escape_string($db, $_POST['bladeLfraction']);
    $bladeLength = (float)$bladeL + (float)$bladeLfraction;
    $bladeW = mysqli_real_escape_string($db, $_POST['bladeWidth']);
    $bladeWfraction = mysqli_real_escape_string($db, $_POST['bladeWfraction']);
    $bladeWidth = (float)$bladeW + (float)$bladeWfraction;

    if ($bladeWidth >0) {
        $query = "INSERT INTO Digger (tool_id, blade_length, blade_width) VALUES ('$tool_id',$bladeLength,$bladeWidth)";
    } else{
        $query = "INSERT INTO Digger (tool_id, blade_length) VALUES ('$tool_id',$bladeLength)";
    }

    $results = mysqli_query($db, $query);
    if ($results == true){
        array_push($query_msg,"Writing tool info to Digger");
    } else{
        array_push($error_msg, "Query Error: Unable write to Digger...". $query);
        array_push($error_msg,  'Error# '. mysqli_errno($db) . ": " . mysqli_error($db));
    }

    if($showQueries){
        array_push($query_msg, "tool ID being used: ". $tool_id);
    }
}

?>

<!DOCTYPE html>  <!-- HTML 5 -->
<head>
    <?php include("lib/header.php"); ?>
    <title>AddTool:Sub-Type Attributes</title>
    <link href="./css/table_new.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_container"><?php include("lib/menuNavClerk.php"); ?>
    <div class="center_content">
        <div class="title_name">Add Digger Type & Subtype Attributes</div>
        <form id="digger" name="digger" method="post" action="add_tool_digger.php">

        <table style="margin-left:10px">
                <tr><td class='heading'>Handle Material:</td></tr>
                <tr><td><input name="handle" type="text" required></td></tr>
            </table>
            <table style="margin-left:10px">
                <tr><td class='heading'>Blade Length (in.)</td><td></td></tr>
                <tr><td><input name="bladeLength" type="number" min="1" max="24" step="1" required></td>
                    <td><select name="bladeLfraction"><option value="0">0</option><option value=".125">1/8</option>
                            <option value=".25">1/4</option><option value=".375">3/8</option><option value=".5">1/2</option>
                            <option value=".625">5/8</option><option value=".75">3/4</option><option value=".875">7/8</option>
                        </select></td></tr>
            </table>
            <table style="margin-left:10px">
                <tr><td class='heading'>Blade Width (in.)</td><td></td></tr>
                <tr><td><input name="bladeWidth" type="number" min="0" max="36" step="1"></td>
                    <td><select name="bladeWfraction"><option value="0">0</option><option value=".125">1/8</option>
                            <option value=".25">1/4</option><option value=".375">3/8</option><option value=".5">1/2</option>
                            <option value=".625">5/8</option><option value=".75">3/4</option><option value=".875">7/8</option>
                        </select></td></tr>
            </table>
            <input style="margin-left:10px; margin-top:10px" type="submit" value="Submit Tool Attributes to Database">
        </form>
        <hr>
        <form action="clerk_menu.php">
            <input style="margin-left:10px" type="submit" value="Done Adding Tool. Go to Clerk Menu">
        </form>
    </div>


    <?php include("lib/error.php"); ?>
    <div class="clear"></div>
<?php include("lib/footer.php"); ?>
</div>
</body>
</html>

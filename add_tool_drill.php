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

    if (isset($_POST['batteryType'])){
        $volt = mysqli_real_escape_string($db, $_POST['batteryVolt']);
    } else{
        $volt = mysqli_real_escape_string($db, $_POST['volt']);
    }

    $speedMin = mysqli_real_escape_string($db, $_POST['speedMin']);
    $speedMax = mysqli_real_escape_string($db, $_POST['speedMax']);
    $torqueMin = mysqli_real_escape_string($db, $_POST['torqueMin']);
    $torqueMax = mysqli_real_escape_string($db, $_POST['torqueMax']);
    $amp = mysqli_real_escape_string($db, $_POST['ampNum']);
    $ampUnit = mysqli_real_escape_string($db, $_POST['ampUnit']);
    $clutchAdj = mysqli_real_escape_string($db, $_POST['adjustableClutch']);
    if ($ampUnit == 'milli'){
        $amp = floatval($amp)/1000;
    } else if ($ampUnit == 'kilo'){
        $amp = floatval($amp)*1000;
    }

    include('./add_tool_queryPower.php');

    if (isset($_POST['batteryType'])){
        $batteryType = mysqli_real_escape_string($db, $_POST['batteryType']);
        $volt = mysqli_real_escape_string($db, $_POST['batteryVolt']);
        $batteryNum = mysqli_real_escape_string($db, $_POST['batteryQuantity']);

        //add tool to Accessories
        $query = "INSERT INTO Accessories (tool_id, accessory_description, quantity) VALUES ('$tool_id',".
            "'D/C Batteries',$batteryNum)";
        $results = mysqli_query($db, $query);

        if ($results == true){
            array_push($query_msg,"Writing battery info to Accessories");
        } else{
            array_push($error_msg, "Query Error: Unable write to Accessories...");
            array_push($error_msg,  'Error# '. mysqli_errno($db) . ": " . mysqli_error($db));
        }
        //add tool to DC_Cordless table
        include('./add_tool_queryDC_Cordless.php');
    }

    if (empty($torqueMax)){
        $torqueMax = 'null';
    }
    $clutchAdj = (bool)$clutchAdj;
    $query = "INSERT INTO Drill (tool_id,adjustable_clutch, min_torque_rating, max_torque_rating) VALUES ".
        "('$tool_id',$clutchAdj,$torqueMin,$torqueMax)";
    $results = mysqli_query($db, $query);
    if ($results == true){
        array_push($query_msg,"Writing tool info to Drill");
    } else{
        array_push($error_msg, "Query Error: Unable write to Drill...". $query);
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
        <div class="title_name">Add Drill Type & Subtype Attributes</div>
        <form id="drill" name="drill" method="post" action="add_tool_drill.php">

        <?php if ($_SESSION['power'] == 'cordless'): ?>
            <div id='batteries'>
            <h3 style="margin-left:10px">Enter Battery Information</h3>
            <table style="margin-left:10px"><tr><td class='heading'>Battery type:</td><td class='heading'>Quantity:</td><td class='heading'>DC Voltage:</td></tr>
            <tr><td><select name='batteryType' required><option value='Li-Ion'>Li-Ion</option><option value='NiCd'>NiCd</option>
            <option value='NiMH'>NiMH</option></select></td>
            <td><input id='batteryQuantity' name='batteryQuantity' type='number' value='1' min='1' max='5' required /></td>
                <td><input id='batteryVolt' name='batteryVolt' type='number' value='18' min='7.2' max='80' step=".1" required /></td></tr></table>
            </div>
            <hr>
        <?php endif; ?>
        <h3 style="margin-left:10px">Add Drill Attributes</h3>
            <table style="margin-left:10px">
                <tr><td class='heading'>Speed Min(rpm):</td><td class='heading'>Speed Max(rpm):</td></tr>
                <tr><td><input name="speedMin" type="number" value="300" min="300" max="9000" required/></td>
                    <td><input name="speedMax" type="number" min="300" max="9000"/></td></tr></table>
            <table style="margin-left:10px">
                <tr><td class='heading'>Torque Min(ft-lb):</td><td class='heading'>Torque Max(ft-lb):</td></tr>
                <tr><td><input name="torqueMin" type="number" value="1" min="1" max="750" step=".1" required/></td>
                    <td><input name="torqueMax" type="number" min="1" max="750" step=".1"/></td></tr></table>
            <table style="margin-left:10px">
                <tr><td class='heading'>Amp Rating:</td><td class='heading'>Amp Units:</td></tr>
                <tr><td><input name="ampNum" type="number" value="1" min="1" max="999" step=".1" required/></td>
                    <td><select name="ampUnit"><option value="milli">mA</option><option value="amps">Amps</option>
                        <option value="kilo">kA</option></select> </td></tr></table>
            <table style="margin-left:10px">
            <?php if ($_SESSION['power'] == 'cordless'): ?>
                <tr><td class='heading'>Volt Rating:</td><td class='heading'>Adjustable Clutch:</td></tr>
                <tr><td><input type="text" name="volt" placeholder="Cordless Battery" disabled/></td>
                    <td><select name="adjustableClutch" required><option></option><option value="true">True</option><option value="false">False</option></select></td></tr>
            <?php else: ?>
                <tr><td class='heading'>Volt Rating (V):</td><td class='heading'>Adjustable Clutch:</td></tr>
                <tr><td><select name="volt" id="volt" required><option value="110">110</option><option value="120">120</option>
                            <option value="220">220</option><option value="220">240</option></td>
                    <td><select name="adjustableClutch" required><option value="true">True</option>
                            <option value="false">False</option></select></td></tr>
            <?php endif; ?>
            </table>
            <input style="margin-left:10px" type="submit" value="Submit Tool Attributes to Database">
        </form>
        <hr>
        <form action="add_drill_accessories.php">
            <input style="margin-left:10px" type="submit" value="Add Accessories for This Power Tool">
        </form>
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

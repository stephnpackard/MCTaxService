<?php
if (!$_GET['id']) {
    header("Location: https://mctaxservice.tealle.com/index?error=nosearch");
}
$page = "home";
include("session.php");
if ($_GET['id']) {
    if ($_GET['edit']) {
        $edit = mysqli_real_escape_string($link,$_GET['edit']);
    }
    $error = "";
    $id = $_GET['id'];
    $date = date("m/d/Y");
    $time = date("h:i a");
    $employeeid = $_SESSION['id'];
    $reason = "";
    $success = 0;
    $id = mysqli_real_escape_string($link,$id);
    if ($_POST['dateBox'] && ($_POST['timeBox']) && ($_POST['reason']) && ($_POST['employee'])) {
        $inputdate = mysqli_real_escape_string($link,$_POST['dateBox']);
        $inputtime = mysqli_real_escape_string($link,$_POST['timeBox']);
        $inputreason = mysqli_real_escape_string($link,$_POST['reason']);
        $inputemployeeID = mysqli_real_escape_string($link,$_POST['employee']);
        if ($edit) {
            mysqli_query($link,
                    "UPDATE `visit`"
                    . "SET `visit_reason` = '$inputreason'"
                    . "WHERE `client_id` = '$id'"
                    . "AND `visit_id` = '$edit'"
                    . "");
            $success = 1;
        }
        else if (!$edit) {
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/',$inputdate,$datematch)) {
            if (preg_match('/(\d{2}:\d{2}\s\S{2})/',$inputtime,$timematch)) {
                if (preg_match('/(\d+)/',$inputemployeeID,$employeeIDmatch)) {
                    $employeeCheck = mysqli_query($link,
                            "SELECT *"
                            . "FROM `employee`"
                            . "WHERE `employee_id`='$inputemployeeID'"
                            . "");
                    $employeeCheckRows = mysqli_num_rows($employeeCheck);
                    if ($employeeCheckRows == 0) {
                        $date = $inputdate;
                        $time = $inputtime;
                        $employeeid = $inputemployeeID;
                        $reason = $inputreason;
                        $error = "Error: That employee ID does not exist.";
                    }
                    else {
                        mysqli_query($link,
                            "INSERT INTO `visit`
                            (`client_id`, `employee_id`,
                              `visit_date`, `visit_time`,
                              `visit_reason`)
                              VALUES ('$id','$inputemployeeID',
                                      '$inputdate', '$inputtime',
                                      '$inputreason')
                             ");
                        $success = 1;
                    }
                }
                else {
                    $date = $inputdate;
                    $time = $inputtime;
                    $employeeid = $inputemployeeID;
                    $reason = $inputreason;
                    $error = "Error: Employee ID entered is not numeric.";
                }
            }
            else {
                $date = $inputdate;
                $time = $inputtime;
                $employeeid = $inputemployeeID;
                $reason = $inputreason;
                $error = "Error: Time entered does not meet hh:mm am/pm format. (Ex. 02:16:28 pm)";
            }
        }
        else {
            $date = $inputdate;
            $time = $inputtime;
            $employeeid = $inputemployeeID;
            $reason = $inputreason;
            $error = "Error: Date entered does not meet mm/dd/yyyy format. (Ex. 08/05/2023)";
        }
    }
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MC Tax Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  </head>
  <body>
      <?php
      include("header.php");
      ?>
      <?php
        $result = mysqli_query($link,
            "SELECT *"
            . "FROM `client`"
            . "WHERE `client_id` = '$id'"
            . "");
        if ($edit) {
            $visitresult = mysqli_query($link,
                "SELECT *
                 FROM `visit`
                 WHERE `client_id` = '$id' AND
                 `visit_id` = '$edit'
                ");
            $visitrows = mysqli_num_rows($visitresult);
            if ($visitrows == 0) {
                echo "<p>Visit not found. Please return to <a href=\"index\">search</a>";
            }
            else {
                while ($visitdata = mysqli_fetch_assoc($visitresult)) {
                    $editdate = $visitdata['visit_date'];
                    $edittime = $visitdata['visit_time'];
                    $editemployee = $visitdata['employee_id'];
                    $editreason = $visitdata['visit_reason'];
                }

            }
        }
        $rows = mysqli_num_rows($result);
        if ($rows == 0) {
            echo "<p>Client not found.  Please return to <a href=\"index\">search</a>.";
        }
        else {
            while ($data = mysqli_fetch_assoc($result)) {
                $client_firstname = $data['client_firstname'];
                $client_lastname = $data['client_lastname'];
            }
            if ($error != "") {
                echo "<p>$error</p>";
            }
            if ($success != 0) {
                if ($edit) {
                    echo "<p>Appointment successfully edited for $client_firstname $client_lastname!<p>";
                }
                else if (!$edit) {
                    echo "<p>Appointment successfully added for $client_firstname $client_lastname!<p>";                    
                }
                echo "<p><a href=\"index\">Return to Client Search</a></p>";
            }
            if ($edit) {
                echo "<p><b>Editing Visit for ".$client_firstname." ".$client_lastname."</b></p>";
                echo "<p>Note: Only the reason can be edited for an already existing visit.</p>";
            }
            else if (!$edit) {
                echo "<p><b>Adding Visit for ".$client_firstname." ".$client_lastname."</b></p>";                
            }
            echo "<p><a href=\"client?id=".$id."\">Return to Client Page</a></p>";
            if ($edit) {
                echo "<form class=\"row g-3\" name=\"visit_add\" action=\"visit?id=$id&edit=$edit\" method=\"post\">";
            }
            else {
                echo "<form class=\"row g-3\" name=\"visit_add\" action=\"visit?id=$id\" method=\"post\">";
            }
            echo "<div class=\"col-md-3\">
            <label for=\"date\">Date:</label>";
            if ($edit) {
                echo "<input type=\"text\" style=\"background-color: #444C54;\" class=\"form-control\" name=\"dateBox\" value=\"$editdate\" required readonly>";
            }
            else if (!$edit) {
                echo "<input type=\"text\" class=\"form-control\" name=\"dateBox\" value=\"$date\" required>";                
            }
        echo "</div>
        <div class=\"col-md-3\">
            <label for=\"time\">Time:</label>";
        if ($edit) {
            echo "<input type=\"text\" style=\"background-color: #444C54;\" class=\"form-control\" name=\"timeBox\" value=\"$edittime\" required readonly>";
        }
        else if (!$edit) {
            echo "<input type=\"text\" class=\"form-control\" name=\"timeBox\" value=\"$time\" required>";
        }
        echo "</div>
        <div></div>
        <div class=\"col-3\">
            <label for=\"employee\">Employee:</label>";
            if ($edit) {
                $employeelist = mysqli_query($link,
                    "SELECT *"
                    . "FROM `employee`"
                    . "WHERE `employee_id` = '$editemployee'"
                    . "");
                echo "<select class=\"form-control form-select\" style=\"background-color: #444C54;\" name=\"employee\" readonly>";
            }
            else if (!$edit) {
                $employeelist = mysqli_query($link,
                    "SELECT *"
                    . "FROM `employee`"
                    . "");
                echo "<select class=\"form-control form-select\" name=\"employee\">";                
            }
            while ($listdata = mysqli_fetch_assoc($employeelist)) {
                if ($edit) {
                    echo "<option value=\"".$listdata['employee_id']."\">".$listdata['employee_firstname']." ".$listdata['employee_lastname']."</option>";
                }
                else if (!$edit) {
                    if ($listdata['employee_id'] != $employeeid) {
                        echo "<option value=\"".$listdata['employee_id']."\">".$listdata['employee_firstname']." ".$listdata['employee_lastname']."</option>";
                    }
                    else {
                        echo "<option value=\"".$listdata['employee_id']."\" selected>".$listdata['employee_firstname']." ".$listdata['employee_lastname']."</option>";
                    }
                }
            }
            echo "</select>";
            //echo "<input type=\"text\" class=\"form-control\" name=\"employeeIDBox\" value=\"$employeeid\" required>
        echo "</div>
        <div></div> 
        <div class=\"col-12\">
            <label for=\"reason\">Reason for Visit:</label>";
        if ($edit) {
            echo "<textarea class=\"form-control\" name=\"reason\" required>$editreason</textarea>";
        }
        else if (!$edit) {
            echo "<textarea class=\"form-control\" name=\"reason\" required>$reason</textarea>";            
        }
        echo "</div>
        <div class=\"col-12\">";
        if ($edit) {
            echo "<button type=\"submit\" class=\"btn btn-primary\" style=\"background-color: #869164; border-color: #BBCB8A\">Update Visit Reason</button>";
        }
        else if (!$edit) {
            echo "<button type=\"submit\" class=\"btn btn-primary\" style=\"background-color: #869164; border-color: #BBCB8A\">Add Visit</button>";
        }
        echo "</div>
      </form>";
        }
      ?>
    </div> <!--container-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
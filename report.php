<?php
$page = "report";
include("session.php");
$date = date("m/d/Y");
if ($_POST['startdate'] && $_POST['enddate']) {
    $inputstartdate = mysqli_real_escape_string($link,$_POST['startdate']);
    $inputenddate = mysqli_real_escape_string($link,$_POST['enddate']);
    if (preg_match('/(\d{2}\/\d{2}\/\d{4})/',$inputstartdate,$datematch)) {
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/',$inputenddate,$datematch)) {
            $error = "";
            $unixtimestamp = strtotime($inputstartdate);
            $dayofweek = date("l",$unixtimestamp);
            $visitresult = mysqli_query($link,
                    "SELECT *"
                    . "FROM `visit`"
                    . "WHERE STR_TO_DATE(`visit_date`,'%m/%d/%Y') BETWEEN STR_TO_DATE('$inputstartdate','%m/%d/%Y') AND STR_TO_DATE('$inputenddate','%m/%d/%Y')"
                    . "");
            $visitrows = mysqli_num_rows($visitresult);
            $visitheader = "Visits between $inputstartdate and $inputenddate.";
            $totalvisits[0] = 0;
            if ($visitrows != 0) {
                while ($visitdata = mysqli_fetch_assoc($visitresult)) {
                    $visitemployee = $visitdata['employee_id'];
                    $visitclient = $visitdata['client_id'];
                    $visitdate = $visitdata['visit_date'];
                    $unixtime = strtotime($visitdate);
                    $visittime = $visitdata['visit_time'];
                    $visithour = explode(":",$visittime);
                    $visitnum = intval($visithour[0]);
                    $dayofweek = date("l",$unixtime);
                    if ($dayofweek == "Sunday") {
                        $sunday[0]++;
                        $sunday[$visitnum]++;
                    }
                    if ($dayofweek == "Monday") {
                        $monday[0]++;
                        $monday[$visitnum]++;
                    }
                    if ($dayofweek == "Tuesday") {
                        $tuesday[0]++;
                        $tuesday[$visitnum]++;
                    }
                    if ($dayofweek == "Wednesday") {
                        $wednesday[0]++;
                        $wednesday[$visitnum]++;
                    }
                    if ($dayofweek == "Thursday") {
                        $thursday[0]++;
                        $thursday[$visitnum]++;
                    }
                    if ($dayofweek == "Friday") {
                        $friday[0]++;
                        $friday[$visitnum]++;
                    }
                    if ($dayofweek == "Saturday") {
                        $saturday[0]++;
                        $saturday[$visitnum]++;
                    }
                    $totalvisits[0]++;
                    $employeeresult = mysqli_query($link,
                        "SELECT *"
                        . "FROM `employee`"
                        . "WHERE `employee_id` = '$visitemployee'"
                        . "");
                    $employeerow = mysqli_fetch_assoc($employeeresult);
                    $employeename = $employeerow['employee_firstname']." ".$employeerow['employee_lastname'];
                    $clientresult = mysqli_query($link,
                        "SELECT *"
                        . "FROM `client`"
                        . "WHERE `client_id` = '$visitclient'"
                        . "");
                    $clientrow = mysqli_fetch_assoc($clientresult);
                    $clientname = $clientrow['client_firstname']." ".$clientrow['client_lastname'];
                    if (!$visits) {
                        $visits = "<table class=\"table table-striped\"><tr><th>Employee</th><th>Client</th><th>Date</th><th>Time</th></tr>";
                    }
                    $visits = $visits."<tr><td>$employeename</td><td>$clientname</td><td>$visitdate</td><td>$visittime</td></tr>";
                }
                $visits = $visits . "</table>";
            }
            else {
                $visits = "none";
            }
        }
        else {
            $error = "The format for the end date is not correct. Date format must be dd/mm/yyyy (i.e. 08/11/2023).";
        }
    }
    else {
        $error = "The format for the start date is not correct. Date format must be dd/mm/yyyy (i.e. 08/11/2023).";
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
      <p>Run report containing information about clients, visitors, and employees.</p>
        <form name="report" action="report" method="post">
            <div class="col-3">
            <label for="startdate" class="form-label">Start Date</label>
                <?php
                if ($_POST['startdate'] && $_POST['enddate']) {
                    echo "<input type=\"text\" class=\"form-control\" name=\"startdate\" value=\"$inputstartdate\" required>";
                }
                else {
                    echo "<input type=\"text\" class=\"form-control\" name=\"startdate\" value=\"$date\" required>";
                }
                ?>
            </div>
            <div class="col-3">
                <label for="enddate" class="form-label">End Date</label>
                <?php
                if ($_POST['startdate'] && $_POST['enddate']) {
                    echo "<input type=\"text\" class=\"form-control\" name=\"enddate\" value=\"$inputenddate\" required>";
                }
                else {
                    echo "<input type=\"text\" class=\"form-control\" name=\"enddate\" value=\"$date\" required>";
                }
                ?>
            </div>
            <br />
            <div class="col-3">
                <button type="submit" class="btn btn-primary" style="background-color: #869164; border-color: #BBCB8A">Run Report</button>
            </div>
        </form>
        <br />
          <?php
          if ($_POST['startdate'] && $_POST['enddate']) {
              if ($error) {
                  echo "<p>$error</p>";
              }
              else {
                if ($visits == "none") {
                   echo "<p>There were no visits between $inputstartdate and $inputenddate</p>";
                }
                else {
                    echo "<p>$visitheader</p>";
                    echo $visits;
                    echo "<p>Visits by Day</p>";
                    for ($count = 1;$count <= 19;$count++) {
                        $totalvisits[$count] = $sunday[$count] + $monday[$count] + $tuesday[$count] + $wednesday[$count] + $thursday[$count] + $friday[$count] + $saturday[$count];
                    }
                    echo "<table class=\"table table-striped\"><tr><th>Date</th><th>Visits</th><th>9AM</th><th>10AM</th><th>11AM</th><th>12PM</th><th>1PM</th><th>2PM</th><th>3PM</th><th>4PM</th><th>5PM</th><th>6PM</th><th>7PM</th></tr>";
                    echo "<tr><td>Sunday</td><td>".(!$sunday[0] ? 0 : $sunday[0])."</td><td>".(!$sunday[9] ? 0 : $sunday[9])."</td><td>".(!$sunday[10] ? 0 : $sunday[10])."</td><td>".(!$sunday[11] ? 0 : $sunday[11])."</td><td>".(!$sunday[12] ? 0 : $sunday[12])."</td><td>".(!$sunday[1] ? 0 : $sunday[1])."</td><td>".(!$sunday[2] ? 0 : $sunday[2])."</td><td>".(!$sunday[3] ? 0 : $sunday[3])."</td><td>".(!$sunday[4] ? 0 : $sunday[4])."</td><td>".(!$sunday[5] ? 0 : $sunday[5])."</td><td>".(!$sunday[6] ? 0 : $sunday[6])."</td><td>".(!$sunday[7] ? 0 : $sunday[7])."</td></tr>";
                    echo "<tr><td>Monday</td><td>".(!$monday[0] ? 0 : $monday[0])."</td><td>".(!$monday[9] ? 0 : $monday[9])."</td><td>".(!$monday[10] ? 0 : $monday[10])."</td><td>".(!$monday[11] ? 0 : $monday[11])."</td><td>".(!$monday[12] ? 0 : $monday[12])."</td><td>".(!$monday[1] ? 0 : $monday[1])."</td><td>".(!$monday[2] ? 0 : $monday[2])."</td><td>".(!$monday[3] ? 0 : $monday[3])."</td><td>".(!$monday[4] ? 0 : $monday[4])."</td><td>".(!$monday[5] ? 0 : $monday[5])."</td><td>".(!$monday[6] ? 0 : $monday[6])."</td><td>".(!$monday[7] ? 0 : $monday[7])."</td></tr>";
                    echo "<tr><td>Tuesday</td><td>".(!$tuesday[0] ? 0 : $tuesday[0])."</td><td>".(!$tuesday[9] ? 0 : $tuesday[9])."</td><td>".(!$tuesday[10] ? 0 : $tuesday[10])."</td><td>".(!$tuesday[11] ? 0 : $tuesday[11])."</td><td>".(!$tuesday[12] ? 0 : $tuesday[12])."</td><td>".(!$tuesday[1] ? 0 : $tuesday[1])."</td><td>".(!$tuesday[2] ? 0 : $tuesday[2])."</td><td>".(!$tuesday[3] ? 0 : $tuesday[3])."</td><td>".(!$tuesday[4] ? 0 : $tuesday[4])."</td><td>".(!$tuesday[5] ? 0 : $tuesday[5])."</td><td>".(!$tuesday[6] ? 0 : $tuesday[6])."</td><td>".(!$tuesday[7] ? 0 : $tuesday[7])."</td></tr>";
                    echo "<tr><td>Wednesday</td><td>".(!$wednesday[0] ? 0 : $wednesday[0])."</td><td>".(!$wednesday[9] ? 0 : $wednesday[9])."</td><td>".(!$wednesday[10] ? 0 : $wednesday[10])."</td><td>".(!$wednesday[11] ? 0 : $wednesday[11])."</td><td>".(!$wednesday[12] ? 0 : $wednesday[12])."</td><td>".(!$wednesday[1] ? 0 : $wednesday[1])."</td><td>".(!$wednesday[2] ? 0 : $wednesday[2])."</td><td>".(!$wednesday[3] ? 0 : $wednesday[3])."</td><td>".(!$wednesday[4] ? 0 : $wednesday[4])."</td><td>".(!$wednesday[5] ? 0 : $wednesday[5])."</td><td>".(!$wednesday[6] ? 0 : $wednesday[6])."</td><td>".(!$wednesday[7] ? 0 : $wednesday[7])."</td></tr>";
                    echo "<tr><td>Thursday</td><td>".(!$thursday[0] ? 0 : $thursday[0])."</td><td>".(!$thursday[9] ? 0 : $thursday[9])."</td><td>".(!$thursday[10] ? 0 : $thursday[10])."</td><td>".(!$thursday[11] ? 0 : $thursday[11])."</td><td>".(!$thursday[12] ? 0 : $thursday[12])."</td><td>".(!$thursday[1] ? 0 : $thursday[1])."</td><td>".(!$thursday[2] ? 0 : $thursday[2])."</td><td>".(!$thursday[3] ? 0 : $thursday[3])."</td><td>".(!$thursday[4] ? 0 : $thursday[4])."</td><td>".(!$thursday[5] ? 0 : $thursday[5])."</td><td>".(!$thursday[6] ? 0 : $thursday[6])."</td><td>".(!$thursday[7] ? 0 : $thursday[7])."</td></tr>";
                    echo "<tr><td>Friday</td><td>".(!$friday[0] ? 0 : $friday[0])."</td><td>".(!$friday[9] ? 0 : $friday[9])."</td><td>".(!$friday[10] ? 0 : $friday[10])."</td><td>".(!$friday[11] ? 0 : $friday[11])."</td><td>".(!$friday[12] ? 0 : $friday[12])."</td><td>".(!$friday[1] ? 0 : $friday[1])."</td><td>".(!$friday[2] ? 0 : $friday[2])."</td><td>".(!$friday[3] ? 0 : $friday[3])."</td><td>".(!$friday[4] ? 0 : $friday[4])."</td><td>".(!$friday[5] ? 0 : $friday[5])."</td><td>".(!$friday[6] ? 0 : $friday[6])."</td><td>".(!$friday[7] ? 0 : $friday[7])."</td></tr>";
                    echo "<tr><td>Saturday</td><td>".(!$saturday[0] ? 0 : $saturday[0])."</td><td>".(!$saturday[9] ? 0 : $saturday[9])."</td><td>".(!$saturday[10] ? 0 : $saturday[10])."</td><td>".(!$saturday[11] ? 0 : $saturday[11])."</td><td>".(!$saturday[12] ? 0 : $saturday[12])."</td><td>".(!$saturday[1] ? 0 : $saturday[1])."</td><td>".(!$saturday[2] ? 0 : $saturday[2])."</td><td>".(!$saturday[3] ? 0 : $saturday[3])."</td><td>".(!$saturday[4] ? 0 : $saturday[4])."</td><td>".(!$saturday[5] ? 0 : $saturday[5])."</td><td>".(!$saturday[6] ? 0 : $saturday[6])."</td><td>".(!$saturday[7] ? 0 : $saturday[7])."</td></tr>";
                    echo "<tr><td>Total Visits</td><td>$totalvisits[0]</td><td>$totalvisits[9]</td><td>$totalvisits[10]</td><td>$totalvisits[11]</td><td>$totalvisits[12]</td><td>$totalvisits[1]</td><td>$totalvisits[2]</td><td>$totalvisits[3]</td><td>$totalvisits[4]</td><td>$totalvisits[5]</td><td>$totalvisits[6]</td><td>$totalvisits[7]</td></tr>";
                    echo "</table></div>";
                }
            }
        }
        ?>
      <!--report-->
    </div>
    <br />
    <br />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
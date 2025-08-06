<?php
function generatePassword($_len) {

    $_alphaSmall = 'abcdefghijklmnopqrstuvwxyz';
    $_alphaCaps  = strtoupper($_alphaSmall); 
    $_numerics   = '1234567890';
    $_specialChars = '`~!@#$%^&*()-_=+]}[{;:,<.>/?\'"\|';

    $_container = $_alphaSmall.$_alphaCaps.$_numerics.$_specialChars;
    $password = ''; 

    for($i = 0; $i < $_len; $i++) {  
        $_rand = rand(0, strlen($_container) - 1); 
        $password .= substr($_container, $_rand, 1);     
    }

    return $password;       // Returns the generated Pass
}
$page = "employee";
include("session.php");
if ($_POST['employee_firstname'] && $_POST['employee_lastname'] && ($_POST['employee_rank'])) {
    $error = "";
    $inputfirstname = $_POST['employee_firstname'];
    $inputlastname = $_POST['employee_lastname'];
    $inputrank = $_POST['employee_rank'];
    $fullname = $inputfirstname." ".$inputlastname;
    $result = mysqli_query($link,"SHOW TABLE STATUS LIKE 'employee'");
    $data = mysqli_fetch_assoc($result);
    $outputID = $data['Auto_increment'];
    if (preg_match('/\A\D+\Z/',$fullname,$namematch)) {
        $temppass = generatePassword(8);
        $options = [
            'cost' => 15
        ];
        $pass = password_hash($temppass, PASSWORD_BCRYPT, $options);
        mysqli_query($link,
                "INSERT INTO `employee`"
                . "(`employee_rank`,"
                . " `employee_firstname`,"
                . " `employee_lastname`,"
                . " `employee_password`,"
                . " `temporary_password`)"
                . "VALUES ('$inputrank',"
                . "'$inputfirstname',"
                . "'$inputlastname',"
                . "'$pass',"
                . "'1')"
                . "");
        $success = "Account has been created for $fullname. <br /><br />Employee ID: $outputID<br />Temporary Password: $temppass<br /><br />This password will need to be changed upon first login.";
    }
    else {
        $error = "Error: Name cannot contain digits.";
    }
}
if ($_POST['password_reset']) {
    $employee_id = $_POST['password_reset'];
    $temppass = generatePassword(8);
    $options = [
        'cost' => 15
    ];
    $pass = password_hash($temppass, PASSWORD_BCRYPT, $options);
    mysqli_query($link,
            "UPDATE `employee`"
            . "SET `employee_password` = '$pass'"
            . "WHERE `employee_id` = '$employee_id'"
            . "");
    $employeeresult = mysqli_query($link,
            "SELECT *"
            . "FROM `employee`"
            . "WHERE `employee_id` = '$employee_id'"
            . "");
    while ($employeedata = mysqli_fetch_assoc($employeeresult)) {
        $fullname = $employeedata['employee_firstname']." ".$employeedata['employee_lastname'];
    }
    $success = "Password has been reset for $fullname. <br /><br />Employee ID: $employee_id<br />Temporary Password: $temppass<br /><br />This password will need to be changed upon next login.";
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
        if ($error) {
            echo $error;
            echo "<br /><br />";
        }
        if ($success) {
            echo $success;
            echo "<br /><br />";
        }
      ?>
      <p>Add Employee</p>
      <form  name="employee_add" action="employee" method="post">
        <div class="col-3">
            <label for="employee_firstname" class="form-label">First Name</label>
            <?php
            if ($error) {
                echo "<input type=\"text\" class=\"form-control\" name=\"employee_firstname\" value=\"$inputfirstname\" required>";                    
            }
            else {
                echo "<input type=\"text\" class=\"form-control\" name=\"employee_firstname\" required>";
            }
            ?>
        </div>
        <div class="col-3">
            <label for="employee_lastname" class="form-label">Last Name</label>
            <?php
            if ($error) {
                echo "<input type=\"text\" class=\"form-control\" name=\"employee_lastname\" value=\"$inputlastname\" required>";                    
            }
            else {
                echo "<input type=\"text\" class=\"form-control\" name=\"employee_lastname\" required>";
            }
            ?>
        </div>
        <div class="col-3">
            <label for="employee_rank" class="form-label">Position</label>
            <select class="form-control form-select" name="employee_rank">
                <option value="employee" selected>Employee</option>
                <?php
                if ($_SESSION['rank'] == "owner") {
                    echo "<option value=\"manager\">Manager</option>";
                    echo "<option value=\"owner\">Owner</option>";
                }
                ?>
            </select>
        </div>
        <br />
        <div class="col-3">
            <button type="submit" class="btn btn-primary" style="background-color: #869164; border-color: #BBCB8A">Add Employee</button>
        </div>
      </form>
      <br /><br />
      <p>Reset Employee Password</p>
      <form name="employee_password_reset" action="employee" method="post">
        <div class="col-3">
            <?php
            $employeelist = mysqli_query($link,
                "SELECT *"
                . "FROM `employee`"
                . "");
            echo "<select class=\"form-control form-select\" name=\"password_reset\">";                
            while ($listdata = mysqli_fetch_assoc($employeelist)) {
                if ($_SESSION['rank'] == "manager" && $listdata['employee_rank'] == "employee") {
                    echo "<option value=\"".$listdata['employee_id']."\">".$listdata['employee_firstname']." ".$listdata['employee_lastname']."</option>";
                }
                else if ($_SESSION['rank'] == "owner") {
                    echo "<option value=\"".$listdata['employee_id']."\">".$listdata['employee_firstname']." ".$listdata['employee_lastname']."</option>";
                }
            }
            ?>
            </select>
        </div>
        <br />
        <div class="col-3">
            <button type="submit" class="btn btn-primary" style="background-color: #869164; border-color: #BBCB8A">Reset Password</button>
        </div>
      </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
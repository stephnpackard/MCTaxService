<?php
session_start();
$error = '';
if (isset($_POST['submit'])) {
    //if employee_id or pass don't exist
    if (empty($_POST['employee_id']) || empty($_POST['pass'])) {
        header("Location: https://mctaxservice.tealle.com/login?error=empty");
    }
    else {
        $options = [
            'cost' => 15
        ];
        //define $employee_id and $password
        $employee_id = $_POST['employee_id'];
        $password = $_POST['pass'];
        //establish connection with server
        include("mysql.php");
        //to protect mysqli injection
        $employee_id = mysqli_real_escape_string($link, $employee_id);
        $password = mysqli_real_escape_string($link, $password);
        //Selecting Database & fetching information
        $query = mysqli_query($link,
                "SELECT *"
                . "FROM `employee`"
                . "WHERE `employee_id` = '$employee_id'"
                . "");
        $rows = mysqli_num_rows($query);
        if ($rows == 0) {
            header("Location: https://mctaxservice.tealle.com/login?error=notfound");
        }
        else {
            //check if password is correct
            while ($data = mysqli_fetch_assoc($query)) {
                if (password_verify($password, $data['employee_password'])) {
                    $passcheck = 1;
                    $firstname = $data['employee_firstname'];
                    $lastname = $data['employee_lastname'];
                    $rank = $data['employee_rank'];
                }
                if (!password_verify($password, $data['employee_password'])) {
                    $passcheck = 0;
                }
            }
            if ($passcheck == 0) {
                header("Location: https://mctaxservice.tealle.com/login?error=wrong");
            }
            else if ($passcheck == 1) {
                //Initializing session
                $_SESSION['login_user'] = $employee_id;
                $_SESSION['name'] = $firstname." ".$lastname;
                if ($rank == "owner") {
                    $_SESSION['rank'] = "owner";
                }
                else if ($rank == "manager") {
                    $_SESSION['rank'] = "manager";
                }
                else if ($rank == "employee") {
                    $_SESSION['rank'] = "employee";
                }
                header("Location: https://mctaxservice.tealle.com/");
            }
            else {
                header("Location: https://mctaxservice.tealle.com/login?error=wrong");
            }
            mysqli_close($link);
        }
    }
}
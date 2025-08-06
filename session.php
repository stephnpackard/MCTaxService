<?php
    date_default_timezone_set("America/Chicago");
    //selecting database
    include("mysql.php");
    //starting session
    session_start();
    //storing session
    $t = time();
    if ($_SESSION['time']) {
        $tdiff = $t - $_SESSION['time'];
        //time out after 5 seconds for testing
        if ($tdiff > 1800) {
            if(session_destroy()) { // Destroying All Sessions
                header("Location: https://mctaxservice.tealle.com/login?error=timedout");
            }
        }
        else {
            $_SESSION['time'] = $t;
        }
    }
    else {
        $_SESSION['time'] = $t;
    }
    $user_check = $_SESSION['login_user'];
    if ($user_check) {
    //query to fetch info of user
        $ses_sql = mysqli_query($link,
            "SELECT *
             FROM `employee`
             WHERE `employee_id`='$user_check'
            ") or die(mysqli_error($link));
        $row = mysqli_fetch_assoc($ses_sql);
        $login_session = $row['employee_id'];
        $rank = $row['employee_rank'];
        $reset = $row['temporary_password'];
        $_SESSION['id'] = $user_check;
        if ($rank == "owner") {
            $_SESSION['rank'] = "owner";
        }
        else if ($rank == "manager") {
            $_SESSION['rank'] = "manager";
        }
        else if ($rank == "employee") {
            $_SESSION['rank'] = "employee";
        }
        if (($page == "employee") && ($_SESSION['rank'] == "employee")) {
            header("Location: https://mctaxservice.tealle.com");
        }
        if (($page == "report") && ($_SESSION['rank'] != "owner")) {
            header("Location: https://mctaxservice.tealle.com");
        }
        if ($reset == 1 && $page != "profile") {
            header("Location: https://mctaxservice.tealle.com/profile?error=reset");
        }
    }
    else {
        if ($page != "login") {
            header("Location: https://mctaxservice.tealle.com/login?error=login");
        }
    }
?>


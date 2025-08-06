<?php   
$page = "profile";
include("session.php");
$employeeid = $_SESSION['id'];
if ($_POST['oldpass'] && ($_POST['pass1'])) {
    if (strlen($_POST['pass1']) >= 8) {
        $result = mysqli_query($link,
            "SELECT `employee_password`, `temporary_password`"
            . "FROM `employee`"
            . "WHERE `employee_id` = '$employeeid'"
            . "");
        while ($data = mysqli_fetch_assoc($result)) {
            $oldpass = $data['employee_password'];
            $temppass = $data['temporary_password'];
        }
        $enteredpass = mysqli_real_escape_string($link,$_POST['oldpass']);
        if (password_verify($enteredpass,$oldpass)) {
            $password = mysqli_real_escape_string($link,$_POST['pass1']);
            $options = [
                'cost' => 15
            ];
            $pass = password_hash($password, PASSWORD_BCRYPT, $options);
           if ($temppass != 1) {
                mysqli_query($link,
                      "UPDATE `employee`"
                        . "SET `employee_password` = '$pass',"
                        . "WHERE `employee_id` = '$employeeid'"
                        . "");
            }
            else {
                mysqli_query($link,
                      "UPDATE `employee`"
                        . "SET `employee_password` = '$pass',"
                        . "`temporary_password` = '0'"
                        . "WHERE `employee_id` = '$employeeid'"
                        . "");
            }
            $success = 1;
        }
        else {
            $error = "Error: Old password does not match.";
        }
    }
    else {
        $error = "Error:  Password is less than 8 characters long.";
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
    <script>
        function checkPass()
        {
            //Store the password field objects into variables ...
            var pass1 = document.getElementById('pass1');
            var pass2 = document.getElementById('pass2');
            //Store the Confimation Message Object ...
            var message = document.getElementById('confirmMessage');
            //Set the colors we will be using ...
            var goodColor = "#265828";
            var badColor = "#920717";
            //Compare the values in the password field
            //and the confirmation field

            if(pass1.value == pass2.value){ 
            ////The passwords match.
                //Set the color to the good color and inform
                //the user that they have entered the correct password
                pass2.style.backgroundColor = goodColor;
                message.style.color = goodColor;
                message.innerHTML = "<button class=\"btn btn-primary\" style=\"background-color: #869164; border-color: #BBCB8A\" type=\"submit\">Change Password</button>"
            }else{
                //The passwords do not match.
                //Set the color to the bad color and
                //notify the user.
                pass2.style.backgroundColor = badColor;
                message.style.color = badColor;
                message.innerHTML = "<button class=\"btn btn-primary\" style=\"background-color: #869164; border-color: #BBCB8A\" type=\"submit\" disabled>Change Password</button> <br /><br />Passwords do not match."
            }
        }  
    </script>
  </head>
  <body>
      <?php
      include("header.php");
      ?>
      <p><b>Change Password</b></p>
      <p>Note: Your password must be at least 8 characters long.</p>
      <?php
      if ($_GET['error'] == "reset") {
          echo "<p>You must change your password before accessing other functions.</p>";
      }
      if ($success) {
          echo "<p>Your password has been changed successfully.</p>";
      }
      if ($error) {
          echo "<p>$error</p>";
      }
      /*$result = mysqli_query($link,
              "SELECT *
               FROM `employee`
               WHERE `employee_id` = '$employeeid'
               ");
      while ($data = mysqli_fetch_assoc($result)) {
          
      }*/
      ?>

      <form class="row g-3" name="changepass" action="profile" method="post">
          <div class="col-3">
              <label for="oldpass">Old Password:</label>
              <input type="password" class="form-control" name="oldpass" required/>
          </div>
          <div></div>
          <div class="col-3">
              <label for="pass1">New Password:</label>
              <input type="password" class="form-control" name="pass1" id="pass1" required/>
          </div>
          <div></div>
          <div class="col-3">
              <label for="pass2">Repeat New Password:</label>
              <input type="password" class="form-control" name="pass2" id="pass2" onkeyup="checkPass(); return false" required/>
          </div>
          <div></div>
          <span id="confirmMessage">
              <button class="btn btn-primary" style="background-color: #869164; border-color: #BBCB8A" type="submit" disabled>Change Password</button>
          </span>
      </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
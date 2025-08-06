<?php
if (!$_GET['id']) {
    header("Location: https://mctaxservice.tealle.com/index?error=nosearch");
}
$page = "home";
include("session.php");
if ($_GET['submit'] == "true") {
    if ($_POST['firstnameBox'] && $_POST['lastnameBox'] && $_POST['phoneBox']) {
        $clientid = mysqli_real_escape_string($link,$_GET['id']);
        $inputfirstname = mysqli_real_escape_string($link,$_POST['firstnameBox']);
        $inputlastname = mysqli_real_escape_string($link,$_POST['lastnameBox']);
        $fullname = $inputfirstname." ".$inputlastname;
        $inputphone = mysqli_real_escape_string($link,$_POST['phoneBox']);
        $inputemail = mysqli_real_escape_string($link,$_POST['emailBox']);
        if (preg_match('/\A\D+\Z/',$fullname,$namematch)) {
            if (preg_match('/(\d{3}(-)?\d{3}(-)?\d{4})/',$inputphone,$phonematch)) {
            if ($inputemail != "") {
                    if (preg_match('/(\S+\@\S+\.\S+)/',$inputemail,$emailmatch)) {
                        mysqli_query($link,
                                "UPDATE `client`"
                                . "SET `client_firstname` = '$inputfirstname',"
                                    . "`client_lastname` = '$inputlastname',"
                                    . "`client_phonenumber` = '$inputphone',"
                                    . "`client_email` = '$inputemail'"
                                . "WHERE `client_id` = '$clientid'"
                                . "");
                        $success = "Client information updated successfully.";
                    }
                    else {
                        $error = "Error: Email format incorrect. (Ex. name@name.com)";
                    }
                }
                else {
                    mysqli_query($link,
                        "UPDATE `client`"
                        . "SET `client_firstname` = '$inputfirstname',"
                            . "`client_lastname` = '$inputlastname',"
                            . "`client_phonenumber` = '$inputphone'"
                        . "WHERE `client_id` = '$clientid'"
                        . "");
                    $success = "Client information updated successfully.";
                }
            }
            else {
                $error = "Error: Phone number format incorrect. (Ex. 123-456-7890 or 1234567890)";
            }
        }
        else {
            $error = "Error: Client name cannot contain digits.";
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
      if ($_GET['id']) {
          $id = $_GET['id'];
          $id = mysqli_real_escape_string($link,$id);
          $result = mysqli_query($link,
                  "SELECT *"
                  . "FROM `client`"
                  . "WHERE `client_id` = '$id'"
                  . "");
          $rows = mysqli_num_rows($result);
          if ($rows == 0) {
              echo "<p>Client not found.  Please return to <a href=\"index\">search</a>.";
          }
          else {
              echo "<b>Client Information</b>";
              if ($_GET['submit'] == "true") {
                  echo "<br /><br />";
                  echo $error;
                  echo $success;
              }
              while ($data = mysqli_fetch_assoc($result)) {
                  if (($_GET['edit'] == "true") && (!$success)) {
                      echo "<br /><br />";
                      echo "<form class=\"row g-3\" name=\"client_edit\" action=\"client?id=$id&edit=true&submit=true\" method=\"post\">";
                      echo "<div class=\"col-md-3\">";
                      echo "<label for=\"firstnameBox\">First Name:</label>";
                      echo "<input type=\"text\" class=\"form-control\" name=\"firstnameBox\" value=\"".$data['client_firstname']."\" required>";
                      echo "</div>";
                      echo "<div class=\"col-md-3\">";
                      echo "<label for=\"lastnameBox\">Last Name:</label>";
                      echo "<input type=\"text\" class=\"form-control\" name=\"lastnameBox\" value=\"".$data['client_lastname']."\" required>";
                      echo "</div>";
                      echo "<div></div>";
                      echo "<div class=\"col-3\">";
                      echo "<label for=\"phoneBox\">Phone Number:</label>";
                      echo "<input type=\"text\" class=\"form-control\" name=\"phoneBox\" value=\"".$data['client_phonenumber']."\" required>";
                      echo "</div>";
                      if ($data['client_email']) {
                          $email = $data['client_email'];
                      }
                      else {
                          $email = "";
                      }
                      echo "<div></div>";
                      echo "<div class=\"col-3\">";
                      echo "<label for=\"emailBox\">E-mail Address (Optional):</label>";
                      echo "<input type=\"text\" class=\"form-control\" name=\"emailBox\" value=\"".$email."\">";
                      echo "</div>";
                      echo "        <div class=\"col-12\">
            <button type=\"submit\" class=\"btn btn-primary\" style=\"background-color: #869164; border-color: #BBCB8A\">Update Customer Information</button>
        </div>";
                      echo "</form>";
                  }
                  else {
                    echo "<br /><br />";
                    echo "Name: ".$data['client_firstname']." ".$data['client_lastname'];
                    echo "<br />";
                    echo "Phone Number: ".$data['client_phonenumber'];
                    if ($data['client_email']) {
                         $email = $data['client_email'];
                      }
                      else {
                         $email = "No e-mail address available.";
                      }
                      echo "<br />";
                      echo "E-mail Address: ".$email;
                  }
              }
              echo "<br /><br />";
              echo "<a href=\"visit?id=".$id."\"><button class=\"btn btn-primary\" style=\"width: 200px; background-color: #869164; border-color: #BBCB8A\">Add Visit</button></a>";
              if (($_GET['edit'] != "true") || ($success)) {
                echo "&nbsp; &nbsp; <a href=\"client?id=$id&edit=true\"><button class=\"btn btn-primary\" style=\"width: 200px; background-color: #869164; border-color: #BBCB8A\">Edit Client Information</button></a>";
              }
          $visitresult = mysqli_query($link,
                  "SELECT *"
                  . "FROM `visit`"
                  . "WHERE `client_id` = '$id'"
                  . "ORDER BY `visit_id` DESC"
                  . "");
          $visitrows = mysqli_num_rows($visitresult);
          echo "<br /><br />";
          if ($visitrows == 0) {
              echo "<p>No previous visits found.</p>";
          }
          else {
              echo "<p><b>Previous Visits</b></p>";
              echo "<table class=\"table table-striped\"><tr><th width=\"10%\">Date</th><th width=\"10%\">Time</th><th width=\"15%\">Employee</th><th width=\"60%\">Reason</th><th></th width=\"5%\"></tr>";
              while ($visitdata = mysqli_fetch_assoc($visitresult)) {
                  $employeeid = $visitdata['employee_id'];
                  $employeeresult = mysqli_query($link,
                          "SELECT `employee_firstname`,"
                          . "`employee_lastname`"
                          . "FROM `employee`"
                          . "WHERE `employee_id` = '$employeeid'"
                          . "");
                  while ($employeedata = mysqli_fetch_assoc($employeeresult)) {
                      $employeefirstname = $employeedata['employee_firstname'];
                      $employeelastname = $employeedata['employee_lastname'];
                  }
                  echo "<tr><td>".$visitdata['visit_date']."</td><td>".$visitdata['visit_time']."</td><td>".$employeefirstname." ".$employeelastname."</td><td>".$visitdata['visit_reason']."</td><td><a class=\"link-body-emphasis\" href=\"visit?id=$id&edit=".$visitdata['visit_id']."\">Edit</a></td></tr>";
              }
          }
        }
      }
      ?>
    </div> <!--container-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
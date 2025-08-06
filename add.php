<?php
$page = "add";
include("session.php");
if ($_POST['client_firstname'] && $_POST['client_lastname'] && $_POST['client_phonenumber']) {
    $inputfirstname = mysqli_real_escape_string($link,$_POST['client_firstname']);
    $inputlastname = mysqli_real_escape_string($link,$_POST['client_lastname']);
    $fullname = $inputfirstname." ".$inputlastname;
    $inputphone = mysqli_real_escape_string($link,$_POST['client_phonenumber']);
    $inputemail = mysqli_real_escape_string($link,$_POST['client_email']);
    if (preg_match('/\A\D+\Z/',$fullname,$namematch)) {
        if (preg_match('/(\d{3}(-)?\d{3}(-)?\d{4})/',$inputphone,$phonematch)) {
            if ($inputemail != "") {
                if (preg_match('/(\S+\@\S+\.\S+)/',$inputemail,$emailmatch)) {
                    mysqli_query($link,
                            "INSERT INTO `client`"
                            . "(`client_firstname`,"
                            . " `client_lastname`,"
                            . " `client_phonenumber`,"
                            . " `client_email`)"
                            . "VALUES ('$inputfirstname',"
                            . "'$inputlastname',"
                            . "'$inputphone',"
                            . "'$inputemail')"
                            . "");
                    $success = "Client added successfully.";
                }
                else {
                    $error = "Error: Email format incorrect. (Ex. name@name.com)";
                }
            }
            else {            
                mysqli_query($link,
                    "INSERT INTO `client`"
                    . "(`client_firstname`,"
                    . " `client_lastname`,"
                    . " `client_phonenumber`)"
                    . "VALUES ('$inputfirstname',"
                    . "'$inputlastname',"
                    . "'$inputphone')"
                    . "") or die(mysqli_error($link));
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
        <form class="row g-3" name="client_add" action="add" method="post">
            <div class="col-md-6">
                <label for="client_firstname" class="form-label">First Name</label>
                <?php
                if ($error) {
                   echo "<input type=\"text\" class=\"form-control\" name=\"client_firstname\" value=\"$inputfirstname\" required>";                    
                }
                else {
                   echo "<input type=\"text\" class=\"form-control\" name=\"client_firstname\" required>";
                }
                ?>
            </div>
            <div class="col-md-6">
                <label for="client_lastname" class="form-label">Last Name</label>
                <?php
                if ($error) {
                    echo "<input type=\"text\" class=\"form-control\" name=\"client_lastname\" value=\"$inputlastname\" required>";                    
                }
                else {
                    echo "<input type=\"text\" class=\"form-control\" name=\"client_lastname\" required>";
                }
                ?>
            </div>
            <div class="col-md-12">
                <label for="client_phonenumber" class="form-label">Phone Number</label>
                <?php
                if ($error) {
                    echo "<input type=\"text\" class=\"form-control\" name=\"client_phonenumber\" value=\"$inputphone\" required>";
                }
                else {
                    echo "<input type=\"text\" class=\"form-control\" name=\"client_phonenumber\" required>";
                }
                ?>
            </div>
            <div class="col-md-12">
                <label for="client_email" class="form-email">E-mail</label>
                <?php
                if ($error) {
                    echo "<input type=\"text\" class=\"form-control\" name=\"client_email\" value=\"$inputemail\">";
                }
                else {
                    echo "<input type=\"text\" class=\"form-control\" name=\"client_email\">";
                }
                ?>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" style="background-color: #869164; border-color: #BBCB8A">Add Client</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>

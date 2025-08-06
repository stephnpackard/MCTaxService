<?php
$page = "login";
include("session.php");
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
      if ($_GET['error'] == "login") {
          echo "<p>You must login before you can access this page.</p>";
      }
      if ($_GET['error'] == "empty") {
          echo "<p>Please fill out both employee ID and password.</p>";
      }
      if ($_GET['error'] == "wrong") {
          echo "<p>Either the employee ID or password was incorrect.  Please try again.</p>";
      }
      if ($_GET['error'] == "timedout") {
          echo "<p>You were idle for more than 30 minutes, please login again.</p>";
      }
      ?>
      <form class="row g-3" name="login" action="loginnew" method="post">
          <div class="col-12">
            <label for=employee_id" class="form-label">Employee ID:</label>
            <br />
            <input type="text" name="employee_id" required>
          </div>
          <div class="col-12">
            <label for="pass" class="form-label">Password:</label>
            <br />
            <input type="password" name="pass" required>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary" name="submit" style="background-color: #869164; border-color: #BBCB8A">Login</button>
          </div>
      </form>
    </div> <!--container-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
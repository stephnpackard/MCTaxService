<?php
$page = "home";
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
      if ($_GET['error'] == "nosearch") {
        echo "<p>No search was entered. Please search again.</p>";
      }
      if ($_GET['error'] == "notfound") {
          echo "<p>Client not found.  Please search again.</p>";
      }
      if ($_GET['success'] == "visit") {
          echo "<p>Visit successfully added!</p>";
      }
      ?>
        <form class="row g-3" name="client_search" action="index" method="post">
            <div class="col-md-4">
                <label for="searchBy" class="form-label">Search by...</label>
                <select name="searchBy" class="form-select">
                    <option selected>Name</option>
                    <option>Phone Number</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label">&nbsp;</label>
                <input type="text" class="form-control" name="searchBox" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" style="background-color: #869164; border-color: #BBCB8A">Search</button>
            </div>
        </form>
    <?php
    include("mysql.php");
    $searchBy = mysqli_real_escape_string($link, $_POST['searchBy']);
    $search = mysqli_real_escape_string($link, $_POST['searchBox']);
    if ((isset($searchBy)) && (isset(($search)))) {
        if ($searchBy == "Name") {
            $result = mysqli_query($link,
                "SELECT *
                    FROM `client`
                    WHERE `client_firstname` LIKE '%$search%' OR
                      `client_lastname` LIKE '%$search%'
                ");
        }
        else if ($searchBy == "Phone Number") {
            $result = mysqli_query($link,
                "SELECT *
                FROM `client`
                WHERE `client_phonenumber` LIKE '%$search%'
            ");
        }
        $rows = mysqli_num_rows($result);
        if ($rows != 0) {
            echo "<table class=\"table table-striped\" style=\"margin-top: 20px;\">";
            echo "<thead><tr><th scope=\"col\">First Name</th><th scope=\"col\">Last Name</th><th scope=\"col\">Phone Number</th><th>E-mail Address</th></tr></thead>";
            while ($data = mysqli_fetch_assoc($result)) {
                $firstname = $data['client_firstname'];
                $lastname = $data['client_lastname'];
                $phone = $data['client_phonenumber'];
                $id = $data['client_id'];
                if ($data['client_email']) {
                    $email = $data['client_email'];
                }
                else {
                    $email = "No e-mail available.";
                }
            echo "<tr><td><a class=\"link-body-emphasis\" href=\"client?id=".$id."\">".$firstname."</a></td><td><a class=\"link-body-emphasis\" href=\"client?id=".$id."\">".$lastname."</a></td><td><a class=\"link-body-emphasis\"  href=\"client?id=".$id."\">".$phone."</a></td><td><a class=\"link-body-emphasis\" href=\"client?id=".$id."\">".$email."</a></td></tr>";
        }  
        echo "</table>";
        }
        else {
            echo "<p style=\"margin-top: 20px;\">No results found. Would you like to <a href=\"add\">add a customer</a>?</p>";
        }
    }
?>
    </div> <!--container-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>

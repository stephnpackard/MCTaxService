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
    $page = "home";
    include("header.php");
    include("mysql.php");
    $searchBy = mysqli_real_escape_string($link, $_POST['searchBy']);
    $search = mysqli_real_escape_string($link, $_POST['searchBox']);
    if ($searchBy == "Name") {
        $result = mysqli_query($link,
            "SELECT `client_firstname`,
                client_lastname,
                client_phonenumber,
                client_email
                FROM `client`
                WHERE `client_firstname` LIKE '%$search%' OR
                      `client_lastname` LIKE '%$search%'
                ");
    }
    else if ($searchBy == "Phone Number") {
        $result = mysqli_query($link,
            "SELECT `client_firstname`,
            client_lastname,
            client_phonenumber,
            client_email
            FROM `client`
            WHERE `client_phonenumber` LIKE '%$search%'
        ");
    }
    $rows = mysqli_num_rows($result);
    if ($rows != 0) {
    echo "<table class=\"table\">";
    echo "<thead><tr><th scope=\"col\">First Name</th><th scope=\"col\">Last Name</th><th scope=\"col\">Phone Number</th><th>E-mail Address</th></tr></thead>";
    while ($data = mysqli_fetch_assoc($result)) {
        $firstname = $data['client_firstname'];
        $lastname = $data['client_lastname'];
        $phone = $data['client_phonenumber'];
        if ($data['client_email']) {
            $email = $data['client_email'];
        }
        else {
            $email = "No e-mail available.";
        }
        echo "<tr><td>".$firstname."</td><td>".$lastname."</td><td>".$phone."</td><td>".$email."</td></tr>";
    }
    echo "</table>";
    }
    else {
        echo "<p>No results found.</p>";
    }
?>
    </div> <!--container-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>

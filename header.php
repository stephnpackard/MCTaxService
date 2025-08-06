    <?php
    $rank = $_SESSION['rank'];
    echo " 
    <nav class=\"navbar navbar-expand-md mb-4\" style=\"background-color: #869164;\">
        <div class=\"container-fluid\">
            <a class=\"navbar-brand\" href=\"index\">MC Tax Service</a>
            <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarCollapse\" aria-controls=\"navbarCollapse\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">
                <span class=\"navbar-toggler-icon\"></span>
            </button>
            <div class=\"collapse navbar-collapse\" id=\"navbarCollapse\">
                <ul class=\"navbar-nav me-auto mb-2 mb-md-0\">
                    <li class=\"nav-item\">";
                    if ($page == "home") {
                        echo "<a class=\"nav-link active\" aria-current=\"page\" href=\"index\">Client Search</a>";
                    }
                    else {
                        echo "<a class=\"nav-link\" href=\"index\">Client Search</a>";
                    }
                    echo "</li>
                    <li class=\"nav-item\">";
                    if ($page == "add") {
                        echo "<a class=\"nav-link active\" aria-current=\"page\" href=\"add\">Add a Client</a>";
                    }
                    else {
                        echo "<a class=\"nav-link\" href=\"add\">Add A Client</a>";
                    }
                    echo "</li>";
                    if (($rank != 'employee') && ($_SESSION['rank'])) {
                        echo "<li class=\"nav-item\">";
                        if ($page == "employee") {
                            echo "<a class=\"nav-link active\" aria-current=\"page\" href=\"employee\">Employees</a>";
                        }
                        else {
                            echo "<a class=\"nav-link\" href=\"employee\">Employees</a>";
                        }
                        echo "</li>";
                    }
                    if ($rank == "owner") {
                        echo "<li class=\"nav-item\">";
                        if ($page == "report") {
                            echo "<a class=\"nav-link active\" aria-current=\"page\" href=\"report\">Report</a>";
                        }
                        else {
                            echo "<a class=\"nav-link\" href=\"report\">Report</a>";
                        }
                        echo "</li>";
                    }
                    echo "</ul>";
                    
                $account = $_SESSION['name'];
                if ($account) {
                echo "
                 <ul class=\"nav navbar-nav ml-auto\">
                 <li class=\"nav-item\">";
                if ($page == "profile") {
                    echo "<a class=\"nav-link active\" aria-current=\"page\" href=\"profile\">Hi, ".$account."</a>";
                }
                else {
                    echo "<a class=\"nav-link\" href=\"profile\">Hi, ".$account."</a>";
                }
                echo "</li>
                    <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"logout\">Logout</a>
                    </li>
                    </ul>";
                }
            echo "</div>
        </div>
    </nav>
    <div class=\"container\">
    ";
    ?>
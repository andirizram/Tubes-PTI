<?php
include '../login/middleware.php';
checkAuthentication(); // Call the middleware function to check authentication

// Database connection
$host = "localhost";
$username = "root";
$password = "andisa8171";
$database = "gempabumi";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle search form submission
if (isset($_POST['search_submit'])) {
  // Get the value of the "NIP" field to search for
  $searchNIP = $_POST['search_nip'];

  // Modify the SQL query to include a WHERE clause for searching
  $sql = "SELECT nama, email, nip FROM users WHERE nip LIKE '%$searchNIP%'";
} else {
  // Default SQL query to fetch all data if the form is not submitted
  $sql = "SELECT nama, email, nip FROM users";
}

// Fetch data from the database based on the SQL query
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Role Admin</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <link rel="stylesheet" href="./style.min.css" />

  <!-- Icons font CSS-->
  <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all" />
  <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all" />
  <!-- Font special for pages-->
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet" />

  <!-- Vendor CSS-->
  <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all" />
  <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all" />

  <!-- Main CSS-->
  <link href="css/main.css" rel="stylesheet" media="all" />
</head>

<body>
  <!-- partial:index.partial.html -->
  <div class="container">
    <h2>Daftar List Admin</h2>
    <div class="card-body">
      <a class="btn-submit" href="../add-user/add-user.php">Tambah</a>
      <form class="form" method="POST" action="#">
        <div class="input-group input--large">
          <label class="label">NIP</label>
          <input class="input--style-1" type="text" placeholder="Nomor NIP" name="search_nip" />
        </div>
        <button class="btn-submit" type="submit" name="search_submit">Search</button>
      </form>
    </div>
    <div class="form-group">
      <!--		Show Numbers Of Rows 		-->
      <select class="form-control" name="state" id="maxRows">
        <option value="10">10</option>
        <option value="15">15</option>
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="70">70</option>
        <option value="100">100</option>
      </select>
    </div>

    <table class="table table-striped table-class" id="table-id">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>NIP</th>
        </tr>
      </thead>

      <tbody>
        <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["nama"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["nip"] . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='3'>Tidak ditemukan</td></tr>";
        }
        ?>
      </tbody>
    </table>

    <!--		Start Pagination -->
    <div class="pagination-container">
      <nav>
        <ul class="pagination">
          <li data-page="prev">
            <span>
              < <span class="sr-only">(current)
            </span></span>
          </li>
          <!--	Here the JS Function Will Add the Rows -->
          <li data-page="next" id="prev">
            <span> > <span class="sr-only">(current)</span></span>
          </li>
        </ul>
      </nav>
    </div>
  </div>
  <!-- 		End of Container -->

  <!--  Developed By Yasser Mas -->
  <!-- partial -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="./script.min.js" async></script>

  <!-- Jquery JS-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <!-- Vendor JS-->
  <script src="vendor/select2/select2.min.js"></script>
  <script src="vendor/jquery-validate/jquery.validate.min.js"></script>
  <script src="vendor/bootstrap-wizard/bootstrap.min.js"></script>
  <script src="vendor/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>

  <!-- Main JS-->
  <script src="js/global.js"></script>
</body>

</html>

<?php
$conn->close();
?>
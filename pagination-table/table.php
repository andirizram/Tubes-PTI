<?php
include '../login/middleware.php';
checkAuthentication(); // Ensuring the user is authenticated

// Database connection
$servername = "localhost";
$username = "root";
$password = "andisa8171";
$dbname = "gempabumi";

// Creating the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Checking the connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Sorting variables
$sort = $_GET['sort'] ?? '';

// Determine the sorting column and order
$sortColumn = '';
$sortOrder = 'ASC';

if (strpos($sort, 'waktu_') === 0) {
  $sortColumn = 'ot_wib';
  if (strpos($sort, 'desc') !== false) {
    $sortOrder = 'DESC';
  }
} elseif (strpos($sort, 'longitude_') === 0) {
  $sortColumn = 'lon';
  if (strpos($sort, 'desc') !== false) {
    $sortOrder = 'DESC';
  }
} elseif (strpos($sort, 'latitude_') === 0) {
  $sortColumn = 'lat';
  if (strpos($sort, 'desc') !== false) {
    $sortOrder = 'DESC';
  }
} elseif (strpos($sort, 'magnitude_') === 0) {
  $sortColumn = 'mag';
  if (strpos($sort, 'desc') !== false) {
    $sortOrder = 'DESC';
  }
} elseif (strpos($sort, 'depth_') === 0) {
  $sortColumn = 'dept';
  if (strpos($sort, 'desc') !== false) {
    $sortOrder = 'DESC';
  }
}

$sql = "SELECT * FROM gempa"; // Default SQL query

// Filtering the results if there's POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Initialize variables
  $lokasi = $start_date = $end_date = '';
  $params = []; // Array to hold parameters for the prepared statement
  $types = ''; // String to hold types for the prepared statement

  // Sanitize and validate user input
  if (!empty($_POST['lokasi'])) {
    $lokasi = $conn->real_escape_string($_POST['lokasi']);
    $params[] = "%{$lokasi}%";
    $types .= 's'; // Type 's' stands for string
  }
  if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    // Validate date format (assuming Y-m-d format, adjust if necessary)
    if (DateTime::createFromFormat('Y-m-d', $start_date) && DateTime::createFromFormat('Y-m-d', $end_date)) {
      $params[] = $start_date;
      $params[] = $end_date;
      $types .= 'ss'; // Type 's' stands for string, twice for two dates
    } else {
      // Handle invalid dates
      die("Invalid date format. Please use 'Y-m-d' format.");
    }
  }

  // Base SQL query
  $sql = "SELECT * FROM gempa WHERE 1=1";

  // Add conditions based on sanitized input
  if (!empty($lokasi)) {
    $sql .= " AND ket LIKE ?";
  }
  if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND ot_wib BETWEEN ? AND ?";
  }

  // Prepare statement
  $stmt = $conn->prepare($sql);

  // Bind parameters dynamically
  if ($types && count($params)) {
    $stmt->bind_param($types, ...$params);
  }

  // Execute the prepared statement
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  // Default SQL query
  $sql = "SELECT * FROM gempa";
  $result = $conn->query($sql);
}

// Handling data download as CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'])) {
  downloadData($_POST['lokasi'], $_POST['start_date'], $_POST['end_date']);
}

function downloadData($lokasi, $start_date, $end_date)
{
  global $conn;

  $sql = "SELECT * FROM gempa WHERE TRUE";

  if (!empty($lokasi)) {
    $sql .= " AND ket LIKE '%$lokasi%'";
  }

  if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND ot_wib BETWEEN '$start_date' AND '$end_date'";
  }

  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="gempa_data.csv"');

    $output = fopen('php://output', 'w');
    // Ensure these column names match your database columns
    fputcsv($output, array('Waktu', 'Longitude', 'Latitude', 'Magnitude', 'Depth (Km)', 'Kota Terdekat', 'Keterangan'), ';');

    while ($row = $result->fetch_assoc()) {
      // Ensure the order of $row values match the header
      fputcsv($output, $row, ';');
    }

    fclose($output);
    exit;
  }
}

// ...
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Table</title>
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
  <style>
    /* Add styling for ascending triangle */
    thead th a.asc::before {
      content: "▲ ";
    }

    /* Add styling for descending triangle */
    thead th a.desc::before {
      content: "▼ ";
    }
  </style>
</head>

<body>
  <!-- partial:index.partial.html -->
  <div class="container">
    <h2>Histori Data Gempa di Sumatera</h2>
    <div class="card-body">
      <form class="form" method="POST" action="" style="float: left; margin-right: 10px;">
        <div class="input-group input--large">
          <label class="label">lokasi</label>
          <input class="input--style-1" type="text" placeholder="Nama Lokasi" name="lokasi" />
        </div>
        <div class="input-group input--medium">
          <label class="label">Waktu Mulai</label>
          <input class="input--style-1" type="date" name="start_date" placeholder="mm/dd/yyyy" id="input-start" />
        </div>
        <div class="input-group input--medium">
          <label class="label">Waktu Berakhir</label>
          <input class="input--style-1" type="date" name="end_date" placeholder="mm/dd/yyyy" id="input-end" />
        </div>
        <button class="btn-submit" type="submit" name="search">Search</button>
      </form>
      <form class="form" method="POST" action="">
        <!-- Hidden fields to keep the current search parameters -->
        <input type="hidden" name="lokasi" value="<?php echo isset($lokasi) ? $lokasi : ''; ?>" />
        <input type="hidden" name="start_date" value="<?php echo isset($start_date) ? $start_date : ''; ?>" />
        <input type="hidden" name="end_date" value="<?php echo isset($end_date) ? $end_date : ''; ?>" />
        <button class="btn-submit" type="submit" name="download" value="1">Download</button>
      </form>
    </div>
    <div class="form-group">
      <!--		Show Numbers Of Rows 		-->
      <select class="form-control" name="state" id="maxRows">
        <option value="10">10</option>
        <option value="50">50</option>
        <option value="100">100</option>
        <option value="500">500</option>
      </select>
    </div>

    <table class="table table-striped table-class" id="table-id">
      <thead>
        <tr>
          <th><a href="?sort=waktu_asc">Waktu ▲</a></th>
          <th><a href="?sort=longitude_asc">Longitude ▲</a></th>
          <th><a href="?sort=latitude_asc">Latitude ▲</a></th>
          <th><a href="?sort=magnitude_asc">Magnitude ▲</a></th>
          <th><a href="?sort=depth_asc">Depth (Km) ▲</a></th>
          <th>Kota Terdekat</th>
          <th>Keterangan</th>
        </tr>
      </thead>

      <tbody>
        <?php
        if (isset($result) && $result->num_rows > 0) {
          // Output data of each row
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
            <td>{$row['ot_wib']}</td>
            <td>{$row['lon']}</td>
            <td>{$row['lat']}</td>
            <td>{$row['mag']}</td>
            <td>{$row['dept']}</td>
            <td>{$row['kota_terdekat']}</td>
            <td>{$row['ket']}</td>
            </tr>";
          }
        } else {
          echo "<tr><td colspan='7'>0 results</td></tr>";
        }
        $conn->close();
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

  <script>
    // Function to get query parameter from the URL
    function getQueryParam(name) {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(name);
    }

    // Function to update the table header links based on the current sorting
    function updateHeaderLinks() {
      const currentSort = getQueryParam("sort");

      // Loop through the table header links and update their text and classes
      document.querySelectorAll("thead th a").forEach((link) => {
        const column = link.getAttribute("href").replace("?sort=", "");
        link.textContent = link.textContent.replace(" ▲", "").replace(" ▼", "");

        // Determine the new sorting order
        let newSortOrder;
        if (currentSort === `${column}_asc`) {
          newSortOrder = "desc";
        } else if (currentSort === `${column}_desc`) {
          newSortOrder = "asc";
        } else {
          newSortOrder = "asc"; // Default sorting order if not sorted by this column
        }

        // Update the link's href with the new sorting order
        link.href = `?sort=${column}_${newSortOrder}`;

        // Add the appropriate classes for styling
        link.classList.remove("asc", "desc");
        link.classList.add(newSortOrder);

        if (currentSort === `${column}_${newSortOrder}`) {
          link.textContent += newSortOrder === "asc" ? ` ▲` : ` ▼`;
        }
      });
    }


    // Execute the updateHeaderLinks function when the page loads
    window.addEventListener("load", updateHeaderLinks);
  </script>
</body>

</html>
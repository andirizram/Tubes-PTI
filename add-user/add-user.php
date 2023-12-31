<?php
include '../login/middleware.php';
checkAuthentication(); // Call the middleware function to check authentication

$host = "localhost";
$username = "root";
$password = "andisa8171";
$database = "gempabumi";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $email = $_POST["email"];
  $nip = $_POST["subject"];
  $password = $_POST["password"];

  // Perform the insertion into the database
  $sql = "INSERT INTO users (nama, email, nip, password) VALUES ('$name', '$email', '$nip', '$password')";

  if ($conn->query($sql) === TRUE) {
    header("Location: ../role/role.php");
    exit();
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,700,900&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="fonts/icomoon/style.css" />

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css" />

  <!-- Style -->
  <link rel="stylesheet" href="css/style.css" />

  <title>Contact Form #6</title>
</head>

<body>
  <div class="content">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="row justify-content-center">
            <div class="col-md-6">
              <h3 class="heading mb-4">
                Silahkan isi data karyawan pada form disamping
              </h3>
              <p>
                <img src="images/undraw-contact.svg" alt="Image" class="img-fluid" />
              </p>
            </div>
            <div class="col-md-6">
              <form class="mb-5" method="post" id="contactForm" name="contactForm">
                <div class="row">
                  <div class="col-md-12 form-group">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan Nama" required />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Masukkan Email" required />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Masukkan NIP" required />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <input type="text" class="form-control" name="password" id="password" placeholder="Masukkan Password" required />
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <input type="submit" value="Tambah User" class="btn btn-primary rounded-0 py-2 px-4" />
                    <span class="submitting"></span>
                  </div>
                </div>
              </form>

              <div id="form-message-warning mt-4"></div>
              <div id="form-message-success">
                Your message was sent, thank you!
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- <script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.validate.min.js"></script> -->
  <script src="js/main.js"></script>
</body>

</html>
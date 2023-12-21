<?php
session_start();

// Destroy the session to log the user out
session_destroy();

// Redirect to the login page (change "login.html" to the actual login page URL)
header("Location: ../login/index.html");
exit();

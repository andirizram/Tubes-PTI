<?php
session_start();

function checkAuthentication() {
    if (!isset($_SESSION['username'])) {
        // Redirect to the login page if the user is not authenticated
        header("Location: ../login/index.html");
        exit();
    }
}

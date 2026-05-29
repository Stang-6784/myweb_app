<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Process the registration logic here
    // For example, you can save the user data to a database

    echo "Registration successful!";
}
?>
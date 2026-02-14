<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "f_evaluation";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Une erreur de connexion est survenue. Veuillez réessayer plus tard.");
}

$conn->set_charset("utf8mb4");

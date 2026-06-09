<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database_name = "oreste_db";

$db = mysqli_connect($hostname, $username, $password, $database_name);

if ($db->connect_error) {
    die("Koneksi database gagal: " . $db->connect_error);
}

mysqli_set_charset($db, "utf8");
?>

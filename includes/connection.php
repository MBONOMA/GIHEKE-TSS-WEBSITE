<?php

$server = "localhost";
$name = "root";
$password = "";
$dbname = "giheke_tss_db";

$conn = mysqli_connect($server, $name, $password, $dbname);


if($conn == FALSE){

    die(mysqli_error($conn));
}




?>
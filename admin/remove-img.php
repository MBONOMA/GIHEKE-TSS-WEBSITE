<?php
session_start();
include('includes/connection.php');
error_reporting(0);  
if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email']))
  { 
header('location:login.php');
}
else{

$id = $_SESSION['admin_id'];

$sql = "update tbl_admins set `ImageUrl` = 'DefaultImage.png' where id='$id'";
$query_run = mysqli_query($conn , $sql);
if($query_run == TRUE){

    echo
    "<script>alert('Image Removed Successfully')</script>";
    header("location:user-profile.php");

}else{


    echo
    "<script>alert('Something Went Wrong Try Again!!!')</script>";

}

}
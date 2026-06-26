<?php


include 'includes/connection.php';

$Level = $_GET['Level'];
$Department = $_GET['Department'];
$id = $_GET['Deleteid'];


if($Level == 'L3' && $Department == 'SOD'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level3_software_development` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L3' && $Department == 'NIT'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level3_computer_networks` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L3' && $Department == 'ELS'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level3_eletronic_services` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L3' && $Department == 'ELC'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level3_industrial_electricity` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L3' && $Department == 'ACC'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level3_professional_accounting` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L3' && $Department == 'CSA'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level3_computer_system` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L3' && $Department == 'BUC'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level3_building_construction` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L4' && $Department == 'SOD'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level4_software_development` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L4' && $Department == 'NIT'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level4_computer_networks` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L4' && $Department == 'ELS'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level4_electronic_services` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L4' && $Department == 'ELC'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level4_industrial_eletricity` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L4' && $Department == 'ACC'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level4_professional_acounting` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L4' && $Department == 'CSA'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level4_computer_system` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L4' && $Department == 'BUC'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level4_building_construction` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L5' && $Department == 'SOD'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level5_software_development` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L5' && $Department == 'NIT'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level5_computer_networks` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L5' && $Department == 'ELS'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level5_eletronic_services` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L5' && $Department == 'ELC'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level5_industrial_electricity` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L5' && $Department == 'ACC'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level5_professional_accounting` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L5' && $Department == 'CSA'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level5_computer_system` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}else if($Level == 'L5' && $Department == 'BUC'){

    $id = $_GET['Deleteid'];

    $sql = "DELETE FROM `level5_buliding_construction` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result){

        header('location:manage-students.php?msg=Student Deleted  Successfully');
    }else{

        header('location:manage-students.php?error=Something Went Wrong');

    }

    
}






<?php
session_start();
include("includes/connection.php");


unset($_SESSION['admin_id']);
unset($_SESSION['admin_Email']);

?>

<script language="javascript">
document.location="login.php";
</script>

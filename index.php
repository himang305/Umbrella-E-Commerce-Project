<?php

session_start();
$_SESSION["location"] = '';
header("Location: routes.php");
exit;
?>
   

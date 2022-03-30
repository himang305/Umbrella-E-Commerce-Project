<?php
require_once 'database.php';
$result = read_db(' * ', 'product_table' , ' where id = '.$_SESSION['product'] );
include 'product.html';


?>


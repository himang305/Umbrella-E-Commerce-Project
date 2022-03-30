<?php

require_once 'database.php';

//print_r($_GET);die();
if (isset($_GET['order'])){
    if((int)$_GET['order'] > 0){
        $result = read_order_list((int)$_GET['order']);
//        print_r($result);die();

        include 'order_list.html';
    }
    }else{
$result = read_orders();
include 'orders.html';
    }
?>


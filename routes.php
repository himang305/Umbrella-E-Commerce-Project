<?php

require_once 'database.php';

if (isset($_GET['company'])) {
    if ((int) $_GET['company'] > 0) {
        session_destroy();
        session_start();
        $result = read_db('*', 'user_table', ' where id = ' . (int) $_GET['company']);
        $row = $result->fetch_assoc();
        if (is_array($row)) {
//  print_r($row); sexit();
            $_SESSION['id'] = $row['id'];
            $_SESSION['company'] = $row['company'];
            $datas = get_company($row['id']);
            include 'website.html';
        }
    }
} else {

    switch ($_SESSION['location']) {
        case "company":
            header("Location:company.php");
            break;
        case "category":
            header("Location:category.php");
            break;
        case "product":
            header("Location:product.php");
            break;
        default:
            include 'homepage.html';
    }
}
?>

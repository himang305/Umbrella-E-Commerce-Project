<?php

session_start();

//print_r($_SESSION);die();

function open_db() {

    $link = mysqli_connect("localhost", "root", "india@123", "test_table");
    if ($link === false) {
        echo ("ERROR: Could not connect. " . mysqli_connect_error());
        exit;
    }
//    echo "Connect Successfully. Host info: " . mysqli_get_host_info($link);

    return $link;
}

function close_db($link) {
    mysqli_close($link);
}

function insert_db($input, $table, $values) {

    //   $sql = "INSERT INTO test_table1 (column1, column2, column3) VALUES ('Peter', 'Parker', 'peterparker@mail.com')";
//echo('sffadas');exit();
    $link = open_db();
    $sql = "INSERT INTO " . $table . "(" . $input . ") VALUES (" . $values . ")";

//if (!$link -> query($sql)) {
//  echo("Error in query: " . $link -> error); 
//}
    $result = mysqli_query($link, $sql);
    return $result;
}

function update_db($input, $table, $values, $where) {

    $link = open_db();
    $input = array_filter(explode("-", $input));
    $values = array_filter(explode("-", $values));
    $set = '';
    for ($i = 1; $i <= count($input); $i++) {
        if ($i > 1) {
            $set = $set . ',';
        }
        $set = $input[$i - 1] . ' = "' . $values[$i - 1] . '"';
    }
    $sql = "UPDATE " . $table . " SET " . $set . " where " . $where;

    $result = mysqli_query($link, $sql);
    return $result;
}

function read_db($input, $table, $where) {
    $link = open_db();
    $sql = "SELECT " . $input . " FROM " . $table . $where;

    $result = mysqli_query($link, $sql);
    return $result;
}

function delete_db($table, $where) {

    $link = open_db();
    $sql = "Delete from " . $table . $where;

    if (mysqli_query($link, $sql)) {
        echo "Records deleted successfully";
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
}

function read_db_user_category() {
    $link = open_db();
    $sql = "SELECT a.sequence as seq, b.* FROM user_table a, category_table b WHERE a.id = b.cid and a.id = " . $_SESSION['id'];
    $result = mysqli_query($link, $sql);
    return $result;
}

function read_db_category_prod() {
    $link = open_db();
    $sql = "SELECT a.sequence as seq, b.* FROM category_table a, product_table b WHERE a.id = b.caid and a.id = " . $_SESSION['category'];
    $result = mysqli_query($link, $sql);
    return $result;
}

function get_company($id) {

    $link = open_db();
    $sql = "SELECT a.id as cid ,a.company, a.image1, a.sequence as seq1 , b.id as caid, b.category, "
            . "b.image2, b.sequence as seq2, c.id, c.product, c.detail, c.image3, c.rating, c.price FROM user_table a"
            . " INNER JOIN category_table b ON a.id = b.cid INNER JOIN product_table c ON b.id = c.caid "
            . "where a.id = " . $id;

    $result = mysqli_query($link, $sql);
    $data1 = [];
    $data2 = [];
    while ($row = $result->fetch_assoc()) {
        $data1[] = $row;
    }
    $sql1 = " SELECT a.* , b.company, b.sequence, b.image1 from category_table a INNER JOIN "
            . "user_table b on a.cid = b.id where b.id = " . $id;
    $result1 = mysqli_query($link, $sql1);
    while ($row1 = $result1->fetch_assoc()) {
        array_push($data2, $row1);
    }
    $data = [$data2, $data1];
    return $data;
}

function get_cart() {

    if (!isset($_SESSION['cart']) || $_SESSION['cart'] == '') {
        return 'Add Products to cart';
    } else {
        $items = implode(',', array_filter(explode(',', $_SESSION['cart'])));
        $link = open_db();
        $sql = "SELECT * from product_table where id IN ( " . $items . ") ";

        $result = mysqli_query($link, $sql);
        return $result;
    }
}

function read_orders() {

    if (!isset($_SESSION['id'])) {
        print_r('Error 432');
        die();
    } else {

        $link = open_db();
        $sql = "SELECT * from order_table where company_id = " . $_SESSION['id'];

        $result = mysqli_query($link, $sql);
        return $result;
    }
}

function read_order_list($id) {

    if ($id == null || $id < 1) {
        print_r('Error 432');
        die();
    } else {

        $link = open_db();
        $sql = "SELECT * from order_table where id = " . $id;

        $result1 = mysqli_query($link, $sql);
        $row = $result1->fetch_assoc();

        $items = implode(',', array_filter(explode(',', $row['item'])));
        $link2 = open_db();
        $sql2 = "SELECT * from product_table where id IN ( " . $items . ") ";

        $result2 = mysqli_query($link2, $sql2);
        while ($row2 = $result2->fetch_assoc()) {
            $row3[] = $row2;
        }

        return [$row, $row3];
    }
}
?>




<?php

require_once 'database.php';

if (isset($_REQUEST['datas'])) {

    $type = (isset($_REQUEST['datas'])) ? $_REQUEST['datas'] : "";
    $types = explode("-", $type);
    $a = (isset($types[0])) ? $types[0] : "";
    $b = (isset($types[1])) ? $types[1] : "";
    $c = (array_key_exists('2', $types)) ? $types[2] : "";
    $d = (array_key_exists('3', $types)) ? $types[3] : "";

    switch ($a) {
        case "login":
            $result = read_db('id,company', 'user_table', ' where username = "' . $b . '" and password = "' . $c . '" ');
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION["id"] = $row['id'];
                $_SESSION["company"] = $row['company'];
                $_SESSION["sequence"] = $row['sequence'];
                $_SESSION["location"] = 'company';
                header("Location: routes.php");
            } else {
                echo " No entry ";
            }
            break;

        case "category_page":

            $_SESSION["category"] = $b;
            $_SESSION["category_name"] = $c;
            $_SESSION["location"] = 'category';
            return;

        case "category_sequence":
            $result = update_db("sequence", 'user_table', $b, ' id = ' . $_SESSION["id"]);
            return $result;

        case "product_page":
            $_SESSION["product"] = $b;
            $_SESSION["location"] = 'product';
            return;

        case "product_sequence":
            $result = update_db("sequence", 'category_table', $b, ' id = ' . $_SESSION["category"]);
            return $result;

        case "add_cart":
            if (!isset($_SESSION['cart']) || $_SESSION['cart'] == null) {
                $_SESSION['cart'] = '';
            }
            $_SESSION['cart'] = $_SESSION['cart'] . ',' . $b;
            return 1;

        case "delete_order":
            if (isset($b) && $b != '') {
                delete_db(' order_table ', ' where id =' . $b);
            }
            return 1;

        default:
            echo "555";
            exit;
    }
} else {
    if (isset($_POST['function'])) {
        switch ($_POST['function']) {
            case "register":

                $upFile = image_upload($_FILES, 'image1');

                if ($upFile != '') {
                    $result = insert_db("company,username,password,image1", 'user_table', '"' . $_POST['company1'] . '","' . $_POST['user1'] . '","' . $_POST['pass1'] . '","' . $upFile . '"');
                    $_SESSION["location"] = '';
                }
//        echo 'saff'; exit;
                header("Location: routes.php");
                break;

            case "register_cat":
                //      echo 'saff'; exit;
                //        print_r($_POST); exit;
                $upFile = '';
                if (isset($_FILES['image2']['size']) && $_FILES['image2']['size'] > 0) {
                    $upFile = image_upload($_FILES, 'image2');
                }
                $result = insert_db("category,cid,image2,sequence", 'category_table', '"' . $_POST['category'] . '","' . $_SESSION["id"] . '","' . $upFile . '",""');
                $_SESSION["location"] = 'company';
                header("Location: routes.php");
                break;

            case "register_product":
                //      echo 'saff'; exit;
                //        print_r($_POST); exit;
                $upFile = '';
                if (isset($_FILES['image3']['size']) && $_FILES['image3']['size'] > 0) {
                    $upFile = image_upload($_FILES, 'image3');
                }
                $result = insert_db("product,detail,caid,cid,image3,sequence,rating,review,price", 'product_table', '"' . $_POST['product'] . '","' . $_POST['detail'] . '","' . $_SESSION["category"] . '","' . $_SESSION["id"] . '","' . $upFile . '","","","",""');
                $_SESSION["location"] = 'category';
                header("Location: routes.php");
                break;

            case "Continue to Checkout":
                if ($_POST['items'] != '') {
                    $result = insert_db("company_id,name,phone,address,item,status", 'order_table', '"' . $_SESSION["id"] . '","' . $_POST['firstname'] . '","' . $_POST['phone'] . '","' . $_POST["address"] . $_POST["address"] . $_POST["address"] . " - " . $_POST["address"] . '","' . $_POST["items"] . '","1"');
                }
//                        print_r($_POST); exit();

                if (isset($_SESSION['id']) && (int) $_SESSION['id'] > 0) {
                    header("Location: routes.php?company=" . $_SESSION['id']);
                    exit;
                } else {
                    session_destroy();
                    header("Location: routes.php");
                }
                return;

            default:
                echo "556";
                exit;
        }
    } else {
        echo "632";
        exit;
    }
}

function image_upload($FILES, $image) {
    if (isset($FILES[$image]["size"]) && $FILES[$image]["size"] > 0) {
        $fileType = $FILES[$image]["type"];
        $fileSize = $FILES[$image]["size"];
        if ($fileSize / 1024 > "1024") {
            echo "Filesize is not correct it should equal to 2 MB or less than 2 MB.";
            exit();
        }
        if (
                $fileType != "image/png" &&
                $fileType != "image/gif" &&
                $fileType != "image/jpg" &&
                $fileType != "image/jpeg" &&
                $fileType != "application/vnd.openxmlformats-officedocument.wordprocessingml.document" &&
                $fileType != "application/zip" &&
                $fileType != "application/pdf"
        ) {
            echo "Sorry this file type is not supported we accept only. Jpeg, Gif, PNG, or ";
            exit();
        }

        $upFile = "uploads/" . date("Y_m_d_H_i_s") . $FILES[$image]["name"];
//        echo $upFile; exit();


        if (is_uploaded_file($FILES[$image]["tmp_name"])) {
            if (!move_uploaded_file($FILES[$image]["tmp_name"], $upFile)) {
                echo "Problem could not move file to destination. Please check again later. <a href='index.php'>Please go back.</a>";
                exit;
            }
        } else {
            echo "Problem: Possible file upload attack. Filename: ";
            echo $FILES[$image]["name"];
            exit;
        }

        return $upFile;
    } else {
        echo 'Wrong File';
    }
}

?>
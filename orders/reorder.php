<?php 
    session_start();
    $user_id = $_SESSION['user_id'];
    include('../config/db_connection.php');
    if(isset($_GET['bkid']) && isset($_GET['pr'])){
        $bkid = $_GET['bkid'];
        $price = $_GET['pr'];
        if($bkid != "" && $price != ""){
            $sql = "INSERT INTO tbl_user_order(user_id) VALUE('$user_id');";
            if(mysqli_query($conn, $sql)){
                $new_order_id = mysqli_insert_id($conn);
                $sql = "INSERT INTO tbl_order_history(order_id, book_id, amount, book_count) VALUES ('$new_order_id', '$bkid', '$price', 1 );";
                if(mysqli_query($conn, $sql)){
                    $next = $_GET['next'];
                    echo "<h3>Successsfully reordered!</h3>";
                    header("refresh: 2; url = $next");
                } else {
                    echo "<h3>Unable to Reorder Book </h3>";

                    header('refresh: 3; url = ./order_history.php');
                }
            }
        }
    }


?>
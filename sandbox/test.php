<?php
    include('../config/db_connection.php');
    include('../functions/functions.php');
    include('../variables/variables.php');
    $sql = "INSERT INTO tbl_user_order(user_id) VALUE(1);";
    if(mysqli_query($conn, $sql)){
        $new_order_id = mysqli_insert_id($conn);
        echo "Last inserted order id was " . $new_order_id;
        $sql = "INSERT INTO tbl_order_history(order_id, book_id, amount, book_count) VALUES('$new_order_id', '1', '23.44', '5'), ('$new_order_id', '2', '23.44', '2'),('$new_order_id', '3', '12.39', '1'),('$new_order_id', '4', '12.48', '2'); ";

        if(mysqli_query($conn, $sql)){
            echo "Successfully inserted";
        } else{
            echo "insert to order history error";
        }

        
    // } else {
    //     echo "create new order id error";
    // }
        $has_address = false;
    // function check_if_book_exist($payment_type, $type, $conn){
    //     include('../users/user_payment_info.php');
    
    //     $check_sql = "SELECT payment_type FROM tbl_user_payment";
    //     $result = mysqli_query($conn, $check_sql);
    //     $payment = mysqli_num_rows($result);
    //     mysqli_free_result($result);
    
    //     return ($payment > 0) ? FALSE : TRUE;
    // }

    $has_address = check_if_address_exists($conn);
    
    
    function check_if_address_exists($conn){
        include('../users/users.php');
    
        $check_sql = "SELECT address_line_1 FROM tbl_user_addresses";
        $result = mysqli_query($conn, $check_sql);
        $address = mysqli_num_rows($result);
        mysqli_free_result($result);
    
        // return ($address > 0) ? FALSE : TRUE;
        if($address == 0){
            return False;
        }else{
            
            return True;
        }
    }
    function check_if_address_exists_1($shipping_type, $type, $conn){
        include('../users/users.php');
    
        $check_sql = "SELECT shipping_method FROM tbl_shipping_methods";
        $result = mysqli_query($conn, $check_sql);
        $shipping = mysqli_num_rows($result);
        mysqli_free_result($result);
    
        return ($shipping > 0) ? FALSE : TRUE;
    }


    
?>
<!DOCTYPE html>
<html lang="en">
    <?php if($has_address) : ?>
        <a href=""><button>Has address</button></a>
        <?php endif; ?>
</html>


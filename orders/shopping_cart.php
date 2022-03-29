<?php 
    session_start();
    $user_id = $_SESSION['user_id'];
    $total = 0;
    include('../config/db_connection.php');
    include('../variables/variables.php');

    $cart_items = retrieve_shopping_cart($tbl_shopping_cart, $user_id, $conn, $tbl_books);
    $payment_method = retrieve_payment_method($tbl_user_payment, $user_id, $conn);
    $address = retrieve_user_address($tbl_user_address, $user_id, $conn);

    if(isset($_POST['check_out'])){
        $sql = "INSERT INTO tbl_user_order(user_id) VALUE('$user_id');";
        // mysqli_query($conn, $sql)
        if(mysqli_query($conn, $sql)){
            $new_order_id = mysqli_insert_id($conn);
            $sql = build_multi_insert_order($new_order_id, $cart_items);
            // echo $sql;
            if(mysqli_query($conn, $sql)){
                $sql = "UPDATE $tbl_shopping_cart SET status = True WHERE user_id=$user_id AND status=False;";
                if(mysqli_query($conn, $sql)){
                    header("Location: ./order_history.php");
                }else {
                    echo '<script>alert("Unable to checking out status!")</script>';
                }

            } else {
                echo '<script>alert("Unable to add to order history!")</script>';
            }
        } else {
            echo '<script>alert("Unable Check Out!")</script>';
        }
    }

    function build_multi_insert_order($lastest_order_id, $cart_items){
        include('../variables/variables.php');
        $multi_orders = "INSERT INTO $tbl_order_history(order_id, book_id, amount, book_count) VALUES ";
        $index = 0;

        foreach($cart_items as $item){
            $book_id = $item['book_id'];
            $amount = $item['amount'];
            $book_count = $item['book_count'];
            if($index == count($cart_items)-1){
                $multi_orders .= "('$lastest_order_id', '$book_id', '$amount', '$book_count' );";
                break;
            }
            $multi_orders .= "('$lastest_order_id', '$book_id', '$amount', '$book_count' ), ";
            $index = $index + 1;
        }
        return $multi_orders;
    }


    function retrieve_shopping_cart($tbl_shopping_cart, $user_id, $conn, $tbl_books){
        /*
            SELECT book_count, amount, tbl_books.book_id, tbl_books.title 
            FROM tbl_shopping_cart
            CROSS JOIN tbl_books
            ON tbl_shopping_cart.book_id = tbl_books.book_id
            WHERE user_id = 1;

            order_history (book_id, amount, book_count)
        */
        $cart = NULL;
        $sql = "SELECT book_count, amount, $tbl_books.book_id, $tbl_books.title  FROM $tbl_shopping_cart CROSS JOIN $tbl_books ON $tbl_shopping_cart.book_id = $tbl_books.book_id WHERE user_id='$user_id' AND status=False;";
        $result = mysqli_query($conn, $sql);
        $items = mysqli_num_rows($result);

        if($items != 0){
            $cart = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        mysqli_free_result($result);
        return $cart;
    }

    function retrieve_payment_method($tbl_user_payment,  $user_id, $conn){
        $payment = NULL;
        $sql = "SELECT payment_type, card_number FROM $tbl_user_payment WHERE user_id=' $user_id';";

        $result = mysqli_query($conn, $sql);
        $items = mysqli_num_rows($result);

        if($items != 0){
            $payment = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        mysqli_free_result($result);
        return $payment;
    }

    function payment_name($type, $card){
        $payment = array("VISA", "MASTER");
        $number = substr($card, strlen($card)-4);
        return "$payment[$type] ending **** $number";
    }

    function retrieve_user_address($tbl_user_address, $user_id, $conn){
        $address = NULL;
        $sql = "SELECT address_line1, address_line2, city, postal_code, country FROM $tbl_user_address WHERE user_id = '$user_id';";

        $result = mysqli_query($conn, $sql);
        $items = mysqli_num_rows($result);

        if($items != 0){
            $address = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        mysqli_free_result($result);
        return $address;
    }

    function concut_address($address){
        $line1 = ($address['address_line1']=="")? "": $address['address_line1']. ', ' ;
        $line2 = ($address['address_line2'] == "") ? "" : $address['address_line2']. ', ' ;
        $city = ($address['city']=="")? "": $address['city']. ', ' ;
        $postal = ($address['postal_code']=="")? "": $address['postal_code']. ', ' ;
        $country = ($address['country']=="")?"": $address['country'];

        $addr = $line1 . $line2 . $city . $postal .$country;
        return $addr;
    }


?>

<!DOCTYPE html>
<html lang="en">
    <?php include('../templates/header.php'); ?>

    <table>
        <th>Item</th>
        <th>Count</th>
        <th>Amount</th>

        <?php 
            if($cart_items != NULL):
            foreach($cart_items as $item):  
        ?>
            <tr>
                <td><?php echo $item['title']; ?></td>
                <td><?php echo $item['book_count'];?></td>
                <td><?php echo $item['amount']; ?></td>
            </tr>
        <?php 
            $total = $total + $item['amount'];
            endforeach;     
            
            endif;
        ?>
    </table>

    <br><hr>
    <tr>
        <td><b>Total:</b> <?php echo '&ensp; $'.$total; ?></td>
    </tr>
    <br><hr>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
        <h3>Current Payment:</h3>
        <div class="input-box">
            <?php if($payment_method != NULL): ?>
            <label for="user_payment">Payment:</label>
            <select name="user_payment" id="">
            
                <?php foreach($payment_method as $p): ?>
                    <option value="pay"><?php echo payment_name($p['payment_type'], $p['card_number']);?></option>
                <?php endforeach; ?>
            </select>
            <?php else: ?>
                <a href="">Add new Payment Method</a>
            <?php endif; ?>
        </div>

        <div class="input-box">
            <h3>Current Address: </h3>
            <?php if( $address != NULL ): ?>
            <label for="user_address">Address:</label>
            <select name="user_address" id="">
                <?php foreach($address as $addr): ?>
                    <option value="addr"><?php echo concut_address($addr) ?></option>
                <?php endforeach;?>
            </select>
            <?php else: ?>
                <a href="">Add new Address</a>
            <?php endif; ?>
        </div>

        <div class="input-box">
            <input type="submit" value="Check out" name="check_out">
        </div>
    </form>

    <?php include('../templates/footer.php'); ?>
</html>
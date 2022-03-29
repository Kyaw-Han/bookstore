<?php

    include('../config/db_connection.php');
    include('../variables/variables.php');
    include('../functions/functions.php');

    //  Session start
    session_start();

    //  Create session variables as HTML entities
    $f_name = htmlspecialchars($_SESSION['f_name']);
    $l_name = htmlspecialchars($_SESSION["l_name"]);
    $author_id = htmlspecialchars($_SESSION['author_id']);
    
    //  Query
    $sql = "SELECT $tbl_users.f_name, $tbl_users.l_name, $tbl_users.username FROM $tbl_users WHERE f_name = '$f_name' AND l_name = '$l_name'";
    //  Make query and get result
    $result = mysqli_query($conn, $sql);
    //  Fetch resulting rows as an assoc array
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);

    //  Query book information based on author
    $sql = "SELECT * FROM $tbl_books WHERE book_id IN (SELECT book_id FROM $tbl_author_books WHERE author_id = '$author_id')";

    $result = mysqli_query($conn, $sql);
    $books_1 = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_free_result($result);

    //  Logout and unset session variables
    if (isset($_POST['log_out'])) {
        unset($_SESSION['f_name']);
        unset($_SESSION['l_name']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_logged_in']);
        unset($_SESSION['payment_type']);
        unset($_SESSION['card_number']);
        unset($_SESSION['card_num']);
        unset($_SESSION['cvv']);
        unset($_SESSION['expiry_date']);
        unset($_SESSION['address_line1']);
        unset($_SESSION['address_line2']);
        unset($_SESSION['city']);
        unset($_SESSION['postal_code']);
        unset($_SESSION['telephone']);
        unset($_SESSION['mobile']);
        unset($_SESSION['country']);
        unset($_SESSION['user_id']);
        unset($_SESSION['author_id']);
        unset($_SESSION['is_author']);
        header('Location:../index.php');
    }
    
    //  Close connection
   // mysqli_close($conn);
?>
<?php 
    include('../templates/header.php');
    function determine_Booktype($type){
        include("../variables/variables.php");
        return $book_types[$type];
    }
?>

<!DOCTYPE html>
<html lang="en">
    <h1>Welcome Back <?php echo $_SESSION['f_name']; ?></h1> <hr>
    <a href ="../users/users-payment-info.php">Manage Payment Info</a> <br><br>
    <a href ="../orders/order_history.php">My Orders</a><br><br>
    <a href ="../orders/shopping_cart.php">My Shopping Cart</a> <br><br>
    <a href ="../premium/users-premium.php">My Benefits/Membership</a><hr>
    <h3>My Books</h3>
    <table>
        <tr>
            <th>Publisher</th>
            <th>Title</th>
            <th>ISBN</th>
            <th>Genre</th>
            <th>Book Type</th>
            <th>Price</th>
            <th>Rating</th>
            <th>Authors</th>
        </tr>
        <?php foreach($books_1 as $book): ?>
            <tr>
                <td><?php echo $book['publisher_name'] ?></td>
                <td><?php echo $book['title'] ?></td>
                <td><?php echo $book['isbn'] ?></td>
                <td><?php echo $book['genre'] ?></td>
                <td><?php echo determine_Booktype($book['book_type']); ?></td>
                <td><?php $number = sprintf('%.2f', $book['price']); echo '$'.$number;  ?></td>
                <td><?php echo $book['rating'] ?></td>
                <td><?php  echo get_author_for_each_book($book['book_id'], $conn);?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
        <div class="submit-buttons">
           <button type="submit" name="log_out">Log out</button>
        </div>
    </form>

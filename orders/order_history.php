<?php 
    session_start();
    $user_id = $_SESSION['user_id'];

    include('../config/db_connection.php');
    include('../variables/variables.php');

    $orders = get_order_history($tbl_order_history, $user_id, $conn);

    function get_order_history($tbl_order_history, $user_id, $conn){
        /*
            SELECT tbl_order_history.*, tbl_books.title 
            FROM tbl_order_history 
            CROSS JOIN tbl_books 
            ON tbl_books.book_id = tbl_order_history.book_id 
            WHERE order_id IN (
                SELECT order_id 
                FROM tbl_user_order 
                WHERE user_id = 1
            );

        */
        $order=NULL;
        $sql = "SELECT tbl_order_history.*, tbl_books.title, tbl_books.price FROM tbl_order_history CROSS JOIN tbl_books ON tbl_books.book_id = tbl_order_history.book_id WHERE order_id IN (SELECT order_id FROM tbl_user_order WHERE user_id = $user_id) ORDER BY created_at DESC;";

        $result = mysqli_query($conn, $sql);
        $items = mysqli_num_rows($result);


        if($items != 0){
            $order = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        mysqli_free_result($result);
        return $order;
    }

?>

<!DOCTYPE html>
<html lang="en">
    <?php include('../templates/header.php'); ?>

    <h2>Order History</h2>
    <hr>

    <table>
        <th>Order ID</th>
        <th>Title</th>
        <th>Comment</th>
        <th>Rating</th>
        <th>Qty</th>
        <th>Amount</th>
        <th>Date</th>
        <th>Add Comment/ Rating</th>
        <th>Reorder</th>

        <?php if($orders != NULL): ?>
            <?php foreach($orders as $order): ?>
            <tr>
                <td><?php echo $order['order_id'] ?></td>
                <td><?php echo $order['title'] ?></td>
                <td><?php echo $order['comment'] ?></td>
                <td><?php echo $order['rating'] ?></td>
                <td><?php echo $order['book_count'] ?></td>
                <td><?php echo $order['amount'] ?></td>
                <td><?php echo $order['created_at'] ?></td>
                <td class="admin"><a href="./comment_rating.php?odrid=<?php echo $order['order_id']; ?>&bkid=<?php echo $order['book_id']; ?>&next=<?php echo $_SERVER['PHP_SELF']; ?>"><button>Add</button></a></td>
                <td class="admin"><a href="./reorder.php?bkid=<?php echo $order['book_id']; ?>&pr=<?php echo $order['price']; ?>&next=<?php echo $_SERVER['PHP_SELF']; ?>"><button>Reorder</button></a></td>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php include('../templates/footer.php'); ?>
</html>




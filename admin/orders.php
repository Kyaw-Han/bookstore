<?php 
    include('../config/db_connection.php');
    include('../variables/variables.php');
    include('../functions/functions.php');
    

    $sql = "SELECT tbl_books.title, tbl_order_history.* FROM tbl_books JOIN tbl_order_history ON tbl_books.book_id = tbl_order_history.book_id ORDER BY created_at DESC";

    $result = mysqli_query($conn, $sql);
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    
?>
<h2>Orders</h2>
<table>
    <th>Order ID</th>
    <th>Book Title</th>
    <th>Qty</th>
    <th>Total</th>
    <th>Comment</th>    
    <th>Rating</th>
    <th>Order Date</th>

    <?php foreach($orders as $order): ?>
        <tr>
            <td><?php echo $order['order_id'] ?></td>
            <td><?php echo $order['title'] ?></td>
            <td><?php echo $order['book_count'] ?></td>
            <td><?php echo '$'.$order['amount'] ?></td>
            <td><?php echo $order['comment'] ?></td>
            <td><?php echo $order['rating'] ?></td>
            <td><?php echo $order['created_at'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

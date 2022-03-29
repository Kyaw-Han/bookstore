<?php 
    session_start();
    $user_id = $_SESSION['user_id'];
    include('../config/db_connection.php');
    include('../variables/variables.php');
    $odrid = "";
    $bkid = "";

    if(isset($_GET['odrid']) && isset($_GET['bkid'])){
        $odrid = $_GET['odrid'];
        $bkid = $_GET['bkid'];
        if($odrid == "" || $bkid == "" || $user_id=""){
            header("Location: ./order_history.php");
        }
    }

    

    if(isset($_POST['add_comment'])){
        $rate = htmlspecialchars($_POST['rating']);
        $comment = htmlspecialchars($_POST['comment']);
        $sql = "UPDATE tbl_order_history SET rating=$rate, comment='$comment' WHERE book_id='$bkid' AND order_id = $odrid";

        if(mysqli_query($conn, $sql)){
            /*
            UPDATE tbl_books AS B JOIN (
                    SELECT AVG(rating) AS avg_rating, book_id FROM tbl_order_history WHERE rating != 0 GROUP BY book_id
                    )AS A 
                    on B.book_id = A.book_id 
                    SET B.rating = A.avg_rating;
            */
            $sql = "
                UPDATE tbl_books AS B JOIN (
                SELECT AVG(rating) AS avg_rating, book_id FROM tbl_order_history WHERE rating != 0 GROUP BY book_id
                )AS A 
                on B.book_id = A.book_id 
                SET B.rating = A.avg_rating;";
            if(mysqli_query($conn, $sql)){
                $next = $_GET['next'];
                header("Location: $next");
            } else {
                echo '<h3>alert("Error Average Rating & Comments!")</h3>';
                header('refresh: 3; url = ./order_history.php');
            }
        }else{
            echo '<h3>alert("Error Adding Rating & Comments!")</h3>';
            header('refresh: 3; url = ./order_history.php');
        }
    }




?>

<!DOCTYPE html>
<html lang="en">
    <?php include('../templates/header.php'); ?>
    <form action="" method="POST">
        <h3>Add Comments/ Rating</h3>
        <div class="input-box">
            <label for="comment">Comment</label>    
            <textarea name="comment" id="" cols="30" rows="10"></textarea>
        </div>
        <div class="input-box">
            <label for="rating">Rating</label>
            <input type="number" name="rating" id="" placeholder="0" step="1" min="1" max="5">
        </div>
        <div class="input-box">
            <input type="submit" value="Add" name="add_comment">
        </div>
    </form>

    <?php include('../templates/footer.php'); ?>
</html>
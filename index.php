<?php
    session_start();
    print_r($_SESSION);
    if(isset( $_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
    }

    include('./config/db_connection.php');

    function determine_Booktype($type){
        include("./variables/variables.php");
        return $book_types[$type];
    }

    if(isset($_POST['publishers'])){
        if(isset($_SESSION['publisher_logged_in']) && $_SESSION['publisher_logged_in']==True){
            header(('Location: ./publishers/publisher.php'));
        }else{
            header('Location: ./login/log-in-publishers.php');
        }
    } else if (isset($_POST['users'])){
        // this is where user log in page goes
        header("Location: ./login/log-in-users.php");
    }else if (isset($_POST['admin'])){
        // this is where admin user goes
        header("Location: ./login/admin.php");
    }

    if(isset($_POST['add_to_cart'])){
        if(!isset($_SESSION['user_logged_in'])){
            $next = $_SERVER['PHP_SELF'];
            header("Location: ./login/log-in-users.php?next=$next");
        }else {
            $bk_id = htmlspecialchars($_POST['bk_id']);
            $bk_pr = htmlspecialchars($_POST['bk_pr']);
            
            $sql = "SELECT book_id FROM tbl_shopping_cart WHERE user_id=$user_id AND book_id=$bk_id AND status = False;";

            if(mysqli_num_rows(mysqli_query($conn, $sql)) > 0){
                $sql = "UPDATE tbl_shopping_cart SET book_count= book_count+1, amount = amount+$bk_pr WHERE user_id=$user_id AND book_id=$bk_id AND status = False;";

                if(mysqli_query($conn, $sql)){
                    header('refresh: 0; url = ./index.php');
                } else{
                    echo '<script>alert("Unable to Add to Cart")</script>';
                }
            }else{

                $sql = "INSERT INTO tbl_shopping_cart(user_id, book_id, book_count, amount) VALUES ('$user_id', '$bk_id', 1, '$bk_pr');";
                if(mysqli_query($conn, $sql)){
                    header('refresh: 0; url = ./index.php');
                } else{
                    echo '<script>alert("Unable to Add to Cart")</script>';
                }
            }
         
        }
    }
    function get_shopping_cart_count($conn, $user_id){
        $sql = "SELECT SUM(book_count) as cart_toal FROM tbl_shopping_cart WHERE user_id = $user_id and status = False;";
        $result = mysqli_query($conn, $sql);
        $count = mysqli_fetch_row($result);
        return $count;
    }

?>

<!DOCTYPE html>
<html lang="en">
    <?php include('./templates/header.php'); ?>

    <div class="index-center">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
            <h3> Log-in</h3>
            <div class="log-in-type">
                <button type="submit" name="users" <?php if(isset($_SESSION['publisher_logged_in']) && $_SESSION['publisher_logged_in']==True) echo 'disabled';  ?> >Users</button>
                <button type="submit" name="publishers" <?php if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']==True) echo 'disabled'; ?> >Publishers</button>
                <button type="submit" name="admin">Admin</button>
            </div>
        </form>
    </div>
    <hr>
    <?php include("./functions/functions.php");?>
    <?php include('./books/search-books.php'); ?>
    <?php if(isset($_SESSION['user_logged_in'])): ?>
        <h2><a href="./orders/shopping_cart.php">Cart:</a> Items: <?php echo (get_shopping_cart_count($conn, $user_id)[0] == 0) ? '0': get_shopping_cart_count($conn, $user_id)[0]; ?></h2>
    <?php endif;?>
    <?php include('./books/display-books.php') ?>

    <?php include('./templates/footer.php'); ?>
</html>
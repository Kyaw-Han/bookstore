<?php 
    include('../config/db_connection.php');

    $bk_id = "";
    $comments = NULL;
    $next = "";
    if(isset($_GET['next'])){
        $next = $_GET['next'];
    }
    if(isset($_GET['id'])){
        $bk_id = $_GET['id'];

        if($bk_id == ""){

        } else {
            $sql = "SELECT comment FROM tbl_order_history WHERE book_id='$bk_id' AND comment != '';";

            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result)>0){
                $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
            mysqli_free_result($result);
        }
    }


?>

<!DOCTYPE html>
<html lang="en">
    <?php include('../templates/header.php');?>

    <h1>Comments...</h1>
    <h3><a style="pointer-events: all;" href="<?php echo $next; ?>"><b>Back</b></a></h3>
    <hr>
    <?php if($comments != NULL): ?>
        <table>
        <?php foreach($comments as $c): ?>
            <tr>
                <td><?php echo $c['comment']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <h3>No comments...</h3>
    <?php endif; ?>

    <?php include('../templates/footer.php');?>
</html>
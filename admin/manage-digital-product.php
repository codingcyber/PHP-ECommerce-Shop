<?php 
require_once('../includes/connect.php');
include('includes/check-login.php'); 
// check the media exists in product_digital table, if exits fetch the media and display in the form or fetch product details and display them in form
$sql = "SELECT * FROM product_digital WHERE id=?";
$result = $db->prepare($sql);
$result->execute(array($_GET['id']));
$mediacount = $result->rowCount();
if($mediacount == 1){
    // fetch the media and display in the form
}else{
    //fetch product details and display them in form 
    $sql = "SELECT * FROM products WHERE id=?";
    $result = $db->prepare($sql);
    $result->execute(array($_GET['id']));
    $product = $result->fetch(PDO::FETCH_ASSOC);
}

if(isset($_POST) & !empty($_POST)){
    // PHP Form Validations
    if(empty($_FILES['media']['name'])){ $errors[] = "You Should Upload Product File";}

    // CSRF Token Validation
    if(isset($_POST['csrf_token'])){
        if($_POST['csrf_token'] == $_SESSION['csrf_token']){
        }else{
            $errors[] = "Problem with CSRF Token Verification";
        }
    }else{
        $errors[] = "Problem with CSRF Token Validation";
    }
    // CSRF Token Time Validation
    $max_time = 60*60*24;
    if(isset($_SESSION['csrf_token_time'])){
        $token_time = $_SESSION['csrf_token_time'];
        if(($token_time + $max_time) >= time()){
        }else{
            $errors[] = "CSRF Token Expired";
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
        }
    }else{
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
    }

    if(empty($errors)){
        // upload the media file
        if(isset($_FILES) & !empty($_FILES)){
            $name = $_FILES['media']['name'];
            $size = $_FILES['media']['size'];
            $type = $_FILES['media']['type'];
            $tmp_name = $_FILES['media']['tmp_name'];

            if(isset($name) & !empty($name)){
                $location = "../product-media/";
                $filename = time() . $name;
                $uploadpath = $location.$filename;
                $dbpath = "product-media/".$filename;
                move_uploaded_file($tmp_name, $uploadpath);
            }
        }
        // check product media, if media exist - create or updte the media
        if($mediacount == 1){
            // update the product media in product_ditial
            echo "Update";
        }else{
            // insert the media in product_ditial
            $sql = "INSERT INTO product_digital (pid, media) VALUES (:pid, :media)";
            $result = $db->prepare($sql);
            $values = array(':pid'    => $_POST['id'],
                            ':media'  => $dbpath
                            );
            $res = $result->execute($values) or die(print_r($result->errorInfo(), true));
            if($res){
                header('location: view-product.php');
            }else{
                $errors[] = "Failed to Add Product Media";
            }
        }
    }
}
// Create CSRF Token
$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time']    = time();
include('includes/header.php');
include('includes/navigation.php');
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Manage Digital Product Media</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Manage Digital Product Media...
                </div>
                <div class="panel-body">
                    <?php
                        if(!empty($errors)){
                            echo "<div class='alert alert-danger'>";
                            foreach ($errors as $error) {
                                echo "<span class='glyphicon glyphicon-remove'></span>&nbsp;". $error . "<br>";
                            }
                            echo "</div>";
                        }
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                <div class="form-group">
                                    <label>Product Title</label>
                                    <input name="title" class="form-control" placeholder="Enter Category Title" value="<?php if(isset($product['title'])){ echo $product['title']; } ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Product Price</label>
                                    <input name="title" class="form-control" placeholder="Enter Category Title" value="<?php if(isset($product['price'])){ echo $product['price']."/-"; } ?>" disabled>
                                </div>
                                <!-- <div class="form-group">
                                    <label>Category Image</label>
                                    <input type="file">
                                </div> -->
                                <div class="form-group">
                                    <label>Product Media</label>
                                    <input type="file" name="media">
                                </div>

                                <input type="submit" class="btn btn-primary" value="Submit" />
                            </form>
                        </div>
                        <!-- /.col-lg-6 (nested) -->   
                    <!-- /.row (nested) -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<?php include('includes/footer.php'); ?>
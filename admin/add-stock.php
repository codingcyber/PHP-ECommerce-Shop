<?php 
require_once('../includes/connect.php');
include('includes/check-login.php'); 

$sql = "SELECT * FROM products WHERE id=?";
$result = $db->prepare($sql);
$result->execute(array($_GET['id']));
$product = $result->fetch(PDO::FETCH_ASSOC);

if(isset($_POST) & !empty($_POST)){
    // PHP Form Validations
    if(empty($_POST['stockin'])){ $errors[] = "Stock field is Required";}

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
        // Insert into product_stock table, after that update the stock in products table
        $sql = "INSERT INTO product_stock (pid, stock_in) VALUES (:pid, :stock_in)";
        $result = $db->prepare($sql);
        $values = array(':pid'          => $_POST['id'],
                        ':stock_in'     => $_POST['stockin']
                        );
        $res = $result->execute($values);
        if($res){
            // update the product stock in products table
            $udpated_stock = $product['stock'] + $_POST['stockin'];
            $updsql = "UPDATE products SET stock=:stock, updated=NOW() WHERE id=:id";
            $updresult = $db->prepare($updsql);
            $values = array(':stock'    => $udpated_stock,
                            ':id'       => $_POST['id']
                            );
            $updresult->execute($values);
            header('location: view-products.php');
        }else{
            $errors[] = "Failed to Add Product Stock";
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
            <h1 class="page-header">Add Product Stock</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Add Product Stock Here...
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
                            <form role="form" method="post">
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
                                    <label>Product Stock</label>
                                    <input type="number" name="stockin" class="form-control" placeholder="Enter Product Stock">
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
<?php 
require_once('../includes/connect.php');
include('includes/check-login.php'); 
if(isset($_POST) & !empty($_POST)){
    // PHP Form Validations
    if(empty($_POST['status'])){ $errors[] = "Status field is Required";}

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
        // Insert into order_status table
        $sql = "INSERT INTO order_status (orderid, status, notes) VALUES (:orderid, :status, :notes)";
        $result = $db->prepare($sql);
        $values = array(':status'       => $_POST['status'],
                        ':notes'        => $_POST['notes'],
                        ':orderid'         => $_POST['orderid']
                        );
        $res = $result->execute($values);
        if($res){
            header('location: view-orders.php');
        }else{
            $errors[] = "Failed to Process the Order";
        }
    }
}
// Create CSRF Token
$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time']    = time();
include('includes/header.php');
include('includes/navigation.php');

$sql = "SELECT o.id, o.created, os.status, o.amount, o.paymentmethod FROM orders o JOIN order_status os ON o.id=os.orderid WHERE o.id=?";
$result = $db->prepare($sql);
$result->execute(array($_GET['id']));
$order = $result->fetch(PDO::FETCH_ASSOC); 
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Process Order</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                   Process the Order Here...
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
                    <table class="cart-table account-table table table-bordered">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Coupon</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $sql = "SELECT * FROM order_items WHERE orderid=?";
                                $result = $db->prepare($sql);
                                $result->execute(array($order['id']));
                                $ordercount = $result->rowCount();
                                $orderitems = $result->fetchAll(PDO::FETCH_ASSOC);
                             ?>
                            <tr>
                                <td>
                                    <?php echo $order['id']; ?>
                                </td>
                                <td>
                                    <?php echo $order['created']; ?>
                                </td>
                                <td>
                                    <?php echo $order['status']; ?>         
                                </td>
                                <td>
                                    &#8377;<?php echo $order['amount']; ?> for <?php echo $ordercount; ?> items             
                                </td>
                                <td>
                                    <?php if(!empty($order['coupon'])){ echo $order['coupon'] ." - ".$order['discount']." Discount Applied.";}else{echo "-"; } ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="cart-table table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            foreach ($orderitems as $orderitem) {
                                // key is id and value is qunatity
                                $sql = "SELECT * FROM products WHERE id=?";
                                $result = $db->prepare($sql);
                                $result->execute(array($orderitem['pid']));
                                $product = $result->fetch(PDO::FETCH_ASSOC);
                         ?>
                            <tr>
                                <td>
                                    <a href="product.php?id=<?php echo $product['id']; ?>"><?php echo $product['title']; ?></a>                 
                                </td>
                                <td>
                                    <span class="amount">&#8377;<?php echo $orderitem['product_price']; ?></span>                   
                                </td>
                                <td>
                                    <div class="quantity"><?php echo $orderitem['product_quantity']; ?></div>
                                </td>
                                <td>
                                    <span class="amount">&#8377;<?php echo ($orderitem['product_price']*$orderitem['product_quantity']); ?></span>                  
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <table class="cart-table account-table table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $sql = "SELECT * FROM order_status WHERE orderid=?";
                                $result = $db->prepare($sql);
                                $result->execute(array($order['id']));
                                $orderstatus = $result->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($orderstatus as $os) {
                             ?>
                            <tr>
                                <td>
                                    <?php echo $os['created']; ?>
                                </td>
                                <td>
                                    <?php echo $os['status']; ?>
                                </td>
                                <td>
                                    <?php echo $os['notes']; ?>         
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-lg-12">
                            <form role="form" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                <input type="hidden" name="orderid" value="<?php echo $_GET['id']; ?>">
                                <div class="form-group">
                                    <label>Order Status</label>
                                    <select name="status" class="form-control">
                                        <option value="Order Placed">Order Placed</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Dispatched">Dispatched</option>
                                        <option value="Delivered">Delivered</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Order Notes</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
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
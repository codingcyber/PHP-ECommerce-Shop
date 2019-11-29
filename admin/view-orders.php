<?php 
require_once('../includes/connect.php');
include('includes/check-login.php');
include('includes/header.php');
include('includes/navigation.php'); 
// Select query to get the order details from orders table
// combine users table to get the username
$sql = "SELECT o.id, o.created, o.amount, u.username FROM orders o JOIN users u ON o.uid=u.id";
$result = $db->prepare($sql);
$result->execute();
$orders = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Orders</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    All the Orders 
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer Name</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($orders as $order) { 
                                    // get the number of order items from order_items table
                                    $sql = "SELECT * FROM order_items WHERE orderid=?";
                                    $result = $db->prepare($sql);
                                    $result->execute(array($order['id']));
                                    $ordercount = $result->rowCount();
                                    // get the order status from order_status table
                                    $sql = "SELECT * FROM order_status WHERE orderid=? ORDER BY id DESC LIMIT 1";
                                    $result = $db->prepare($sql);
                                    $result->execute(array($order['id']));
                                    $status = $result->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo $order['username']; ?></td>
                                    <td><?php echo $order['created']; ?></td>
                                    <td><?php echo $order['amount']; ?></td>
                                    <td><?php echo $ordercount; ?></td>
                                    <td><?php echo $status['status']; ?></td>
                                    <td><a href="process-order.php?id=<?php echo $order['id']; ?>">Process Order</a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
            </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<?php include('includes/footer.php'); ?>
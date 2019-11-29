<?php 
session_start();
require_once('includes/connect.php');
// check user login - customer
require_once('includes/check-login.php');
// fetch orders from orders table and order_status table based on loggedin user session id

$sql = "SELECT id, created, amount FROM orders WHERE uid=?";
$result = $db->prepare($sql);
$result->execute(array($_SESSION['id']));
$orders = $result->fetchAll(PDO::FETCH_ASSOC);
include('includes/header.php'); 
?>
<!-- SHOP CONTENT -->
<section id="content">
	<div class="content-blog content-account">
		<div class="container">
			<div class="row">
				<div class="page_header text-center">
					<h2>My Account</h2>
				</div>
				<div class="col-md-12">

		<h3>Recent Orders</h3>
		<br>
		<table class="cart-table account-table table table-bordered">
			<thead>
				<tr>
					<th>Order</th>
					<th>Date</th>
					<th>Status</th>
					<th>Total</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php 
					foreach ($orders as $order) {
						$sql = "SELECT * FROM order_items WHERE orderid=?";
						$result = $db->prepare($sql);
						$result->execute(array($order['id']));
						$itemscount = $result->rowCount();

						$sql = "SELECT * FROM order_status WHERE orderid=? ORDER BY id DESC LIMIT 1";
						$result = $db->prepare($sql);
						$result->execute(array($order['id']));
						$status = $result->fetch(PDO::FETCH_ASSOC);
				 ?>
				<tr>
					<td>
						<?php echo $order['id']; ?>
					</td>
					<td>
						<?php echo $order['created']; ?>
					</td>
					<td>
						<?php echo $status['status']; ?>			
					</td>
					<td>
						&#8377;<?php echo $order['amount']; ?> for <?php echo $itemscount; ?> items				
					</td>
					<td>
						<a href="view-order.php?id=<?php echo $order['id']; ?>">View</a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>		

		<br>
		<br>
		<br>

		<div class="ma-address">
					<h3>My Addresses <a href="add-address.php" class="pull-right">Add New Address</a></h3>
					<p>The following addresses will be used on the checkout page by default.</p>

		<div class="row">
			<?php 
				$sql = "SELECT * FROM user_address WHERE uid=?";
			    $result = $db->prepare($sql);
			    $result->execute(array($_SESSION['id']));
			    $res = $result->fetchAll(PDO::FETCH_ASSOC);
			    foreach ($res as $address) {
			 ?>
			<div class="col-md-3">
				<h4><?php echo $address['nickname']; ?> <a href="manage-address.php?id=<?php echo $address['id']; ?>">Edit</a></h4>
				<p>
					<?php echo $address['fname']." ".$address['lname']; ?><br>
					<?php echo $address['phone']; ?><br>
					<?php echo $address['address1']; ?><br>
					<?php echo $address['address2']; ?><br>
					<?php echo $address['city']; ?><br>
					<?php echo $address['state']; ?><br>
					<?php echo $address['country']; ?><br>
					<?php echo $address['zipcode']; ?>
				</p>
			</div>
			<?php } ?>
		</div>
		</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('includes/footer.php'); ?>
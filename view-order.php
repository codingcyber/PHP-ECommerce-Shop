<?php 
session_start();
require_once('includes/connect.php');
require_once('includes/check-login.php');
include('includes/header.php'); 

$sql = "SELECT o.id, o.created, os.status, o.amount, o.paymentmethod FROM orders o JOIN order_status os ON o.id=os.orderid WHERE o.id=?";
$result = $db->prepare($sql);
$result->execute(array($_GET['id']));
$order = $result->fetch(PDO::FETCH_ASSOC); 
?>
<!-- SHOP CONTENT -->
<section id="content">
	<div class="content-blog">
		<div class="container">
			<div class="row">
				<div class="page_header text-center">
					<h2>View Order</h2>
					<p>Your Order Details are Here...</p>
				</div>
				<div class="col-md-12">
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
					<th>&nbsp;</th>
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
						<a href="product.php?id=<?php echo $product['id']; ?>"><img src="<?php echo $product['image']; ?>" alt="" height="90" width="90"></a>					
					</td>
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

		<div class="cart_totals">
			<div class="col-md-6 push-md-6 no-padding">
				<h4 class="heading">Cart Totals</h4>
				<table class="table table-bordered col-md-6">
					<tbody>
						<tr>
							<th>Cart Subtotal</th>
							<td><span class="amount">&#8377;<?php echo $order['amount']; ?></span></td>
						</tr>
						<tr>
							<th>Payment Method</th>
							<td>
								<?php echo $order['paymentmethod']; ?>				
							</td>
						</tr>
						<tr>
							<th>Order Total</th>
							<td><strong><span class="amount">&#8377;<?php echo $order['amount']; ?></span></strong> </td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>			
						
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('includes/footer.php'); ?>
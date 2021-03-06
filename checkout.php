<?php 
session_start();
require_once('includes/connect.php');
// check user login - customer
require_once('includes/check-login.php'); 
$date = date("Y-m-d");
if(isset($_SESSION['cart'])){

}else{
	header('location: cart.php');
}

if(isset($_POST) & !empty($_POST)){
	// PHP Form Validations
	if(empty($_POST['address'])){ $errors[]="Choose an Address"; }
	if(empty($_POST['payment'])){ $errors[]="Select a Payment Method"; }

	// CSRF Token Validation
    if(isset($_POST['csrf_token'])){
        if($_POST['csrf_token'] === $_SESSION['csrf_token']){
        }else{
            $errors[] = "Problem with CSRF Token Validation";
        }
    }
    // CSRF Token Time Validation
    $max_time = 60*60*24; // in seconds
    if(isset($_SESSION['csrf_token_time'])){
        $token_time = $_SESSION['csrf_token_time'];
        if(($token_time + $max_time) >= time() ){
        }else{
            $errors[] = "CSRF Token Expired";
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
        }
    }

    if(empty($errors)){
    	// Insert the Order in orders table
    	$cart = $_SESSION['cart'];
    	$total = 0;
		foreach ($cart as $key => $value) {
			// key is id and value is qunatity
			$sql = "SELECT * FROM products WHERE id=?";
		    $result = $db->prepare($sql);
		    $result->execute(array($key));
		    $product = $result->fetch(PDO::FETCH_ASSOC);

		    $total = $total + ($product['price']*$value['quantity']);
		}

		if(isset($_SESSION['coupon'])){
			$sql = "SELECT * FROM coupons WHERE coupon_code=? AND DATE(coupon_expiry) >= $date";
			$result = $db->prepare($sql);
			$result->execute(array($_SESSION['coupon']));
			$count = $result->rowCount();
			$coupon = $result->fetch(PDO::FETCH_ASSOC);
			if($coupon['type'] == 'percentage'){
				//(coupon value / 100) * total
				$discount = ($coupon['coupon_value']/100) * $total;

			}elseif($coupon['type'] == 'flat-rate'){
				$discount = $coupon['coupon_value'];
			}
		}

		$ordersql = "INSERT INTO orders (uid, add_id, amount, paymentmethod";
		if(isset($_SESSION['coupon'])){$ordersql .= ", coupon, discount";}
		$ordersql .= ") VALUES (:uid, :add_id, :amount, :paymentmethod";
		if(isset($_SESSION['coupon'])){$ordersql .= ", :coupon, :discount";}
		$ordersql .= ")";
        $orderresult = $db->prepare($ordersql);
        $values = array(':uid'     			=> $_SESSION['id'],
                        ':add_id'     		=> $_POST['address'],
                        ':amount'       	=> $total,
                        ':paymentmethod'    => $_POST['payment']
                        );
        if(isset($_SESSION['coupon'])){
        	$values[':coupon'] = $coupon['coupon_code'];
        	$values[':discount'] = $discount; 
        }
        $orderres = $orderresult->execute($values) or die(print_r($orderresult->errorInfo(), true));
        if($orderres){
        	//$messages[] = 'Order Placed';
        	$orderid = $db->lastInsertID();
        	// Insert the Product Items into order_items table with Order Id
        	foreach ($cart as $key => $value) {
				// key is id and value is qunatity
				$productsql = "SELECT * FROM products WHERE id=?";
			    $productresult = $db->prepare($productsql);
			    $productresult->execute(array($key));
			    $productres = $productresult->fetch(PDO::FETCH_ASSOC);

			    $orderitemsql = "INSERT INTO order_items (pid, orderid, product_price, product_quantity) VALUES (:pid, :orderid, :product_price, :product_quantity)";
		        $orderitemresult = $db->prepare($orderitemsql);
		        $values = array(':pid'     			=> $productres['id'],
		                        ':orderid'     		=> $orderid,
		                        ':product_price'    => $productres['price'],
		                        ':product_quantity' => $value['quantity']
		                        );
		        $orderitemres = $orderitemresult->execute($values) or die(print_r($orderitemresult->errorInfo(), true));
			    
			}

			if(isset($_SESSION['coupon'])){
		        // Insert into coupon_redemptions table
		        $couponsql = "INSERT INTO coupon_redemptions (cid, `oid`, uid) VALUES (:cid, :orderid, :uid)";
		        $couponresult = $db->prepare($couponsql);
		        $values = array(':cid'   	=> $coupon['id'],
		                        ':orderid'	=> $orderid,
		                        ':uid'    	=> $_SESSION['id']
		                        );
		        $couponres = $couponresult->execute($values) or die(print_r($couponresult->errorInfo(), true));

		        // update coupon limit number by decrementing
		        $coupon_limit = $coupon['coupon_limit'] - 1;
		        $updsql = "UPDATE coupons SET coupon_limit=:coupon_limit, updated=NOW() WHERE id=:id";
		        $updresult = $db->prepare($updsql);
		        $values = array(':coupon_limit'	=> $coupon_limit,
		                        ':id'       => $coupon['id']
		                        );
		        $updres = $updresult->execute($values) or die(print_r($updresult->errorInfo(), true));
	    	}
    		// Insert the Order_status with order id
    		$orderstatussql = "INSERT INTO order_status (orderid, status, notes) VALUES (:orderid, :status, :notes)";
	        $orderstatusresult = $db->prepare($orderstatussql);
	        $values = array(':orderid'     		=> $orderid,
	                        ':status'     		=> 'Order Placed',
	                        ':notes'       		=> $_POST['ordernotes']
	                        );
	        $orderstatusres = $orderstatusresult->execute($values) or die(print_r($result->errorInfo(), true));
	        if($orderstatusres){
	        	// remove items from cart session and redirect to my account page
	        	unset($_SESSION['cart']);
	        	unset($_SESSION['coupon']);
	        	header('location: my-account.php');
	        }
        }
    }
}
// Create CSRF token
$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time'] = time();
include('includes/header.php');
?>
<!-- SHOP CONTENT -->
<section id="content">
	<div class="content-blog">
		<div class="page_header text-center">
			<h2>Shop - Checkout</h2>
			<p>Make the Payment to Place the Order</p>
		</div>
	<form method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php
                    if(!empty($errors)){
                        echo "<div class='alert alert-danger'>";
                        foreach ($errors as $error) {
                            echo "&nbsp;".$error."<br>";
                        }
                        echo "</div>";
                    }
                ?>
				<div class="billing-details">
					<h3 class="uppercase">Choose an Address <a href="add-address.php" class="pull-right">Add New Address</a></h3>
					<div class="space30"></div>
					<?php 
						$sql = "SELECT * FROM user_address WHERE uid=?";
					    $result = $db->prepare($sql);
					    $result->execute(array($_SESSION['id']));
					    $res = $result->fetchAll(PDO::FETCH_ASSOC);
					    foreach ($res as $address) {
					 ?>
					<div class="col-md-3">
						<h4><input type="radio" name="address" value="<?php echo $address['id']; ?>"><?php echo $address['nickname']; ?></h4>
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
		
		<div class="space30"></div>
		<h4 class="heading">Your order</h4>
		
		<table class="table table-bordered extra-padding">
			<?php 
				$cart = $_SESSION['cart'];
		    	$total = 0;
				foreach ($cart as $key => $value) {
					// key is id and value is qunatity
					$sql = "SELECT * FROM products WHERE id=?";
				    $result = $db->prepare($sql);
				    $result->execute(array($key));
				    $product = $result->fetch(PDO::FETCH_ASSOC);

				    $total = $total + ($product['price']*$value['quantity']);
				}
			 ?>
			<tbody>
				<tr>
					<th>Cart Subtotal</th>
					<td><span class="amount">&#8377;<?php echo $total; ?></span></td>
				</tr>
				<tr>
					<th>Shipping and Handling</th>
					<td>
						Free Shipping				
					</td>
				</tr>
				<?php 
				if(isset($_SESSION['coupon'])){
					$sql = "SELECT * FROM coupons WHERE coupon_code=? AND DATE(coupon_expiry) >= $date";
					$result = $db->prepare($sql);
					$result->execute(array($_SESSION['coupon']));
					$count = $result->rowCount();
					$coupon = $result->fetch(PDO::FETCH_ASSOC);
				?>
				<tr>
					<th>Discount <small>(<?php echo $coupon['coupon_code']; ?>)</small></th>
					<td>
						<?php 
							if($coupon['type'] == 'percentage'){
								//(coupon value / 100) * total
								$discount = ($coupon['coupon_value']/100) * $total;
								$total = $total - $discount;

							}elseif($coupon['type'] == 'flat-rate'){
								$discount = $coupon['coupon_value'];
								$total = $total - $discount;
							}
						?>
						&#8377; <?php echo $discount; ?>				
					</td>
				</tr>
				<?php } ?>

				<tr>
					<th>Order Total</th>
					<td><strong><span class="amount">&#8377;<?php echo $total; ?></span></strong> </td>
				</tr>
			</tbody>
		</table>
		<div class="clearfix space20"></div>
		<label>Order Notes</label>
		<textarea name="ordernotes" class="form-control" placeholder="Notes about your order, e.g. special notes for delivery." rows="4" cols="5"></textarea>
		<div class="clearfix space20"></div>
		
		<div class="clearfix space30"></div>
		<h4 class="heading">Payment Method</h4>
		<div class="clearfix space20"></div>
		
		<div class="payment-method">
			<div class="row">
				<form>
					<div class="col-md-4">
						<input name="payment" id="radio1" class="css-checkbox" type="radio" value="bank"><span>Direct Bank Transfer</span>
						<div class="space20"></div>
						<p>Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won't be shipped until the funds have cleared in our account.</p>
					</div>
					<div class="col-md-4">
						<input name="payment" id="radio2" class="css-checkbox" type="radio" value="cheque"><span>Cheque Payment</span>
						<div class="space20"></div>
						<p>Please send your cheque to Your Company Address.</p>
					</div>
					<div class="col-md-4">
						<input name="payment" id="radio3" class="css-checkbox" type="radio" value="paypal"><span>Paypal</span>
						<div class="space20"></div>
						<p>Pay via PayPal; you can pay with your credit card if you don't have a PayPal account</p>
					</div>
				</form>
			</div>
			<div class="space30"></div>
			<form>
				<input name="terms" id="checkboxG2" class="css-checkbox" type="checkbox"><span>I've read and accept the <a href="#">terms &amp; conditions</a></span>
			</form>
			<div class="space30"></div>
			<input type="submit" class="button btn-lg" value="Pay Now" />
		</div>
	</div>		
	</form>
	</div>
</section>
<?php include('includes/footer.php'); ?>
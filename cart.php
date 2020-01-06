<?php 
session_start();
require_once('includes/connect.php');
include('includes/header.php'); 
if(isset($_POST) & !empty($_POST)){
	$sql = "SELECT * FROM coupons WHERE coupon_code=?";
	$result = $db->prepare($sql);
	$result->execute(array($_POST['coupon']));
	$count = $result->rowCount();
	$coupon = $result->fetch(PDO::FETCH_ASSOC);
	if($count == 1){
		// create the sesison with coupon_code
		$_SESSION['coupon']	= $coupon['coupon_code'];
	}else{
		$couponerrors[] = "Invalid Coupon Code";
	}
}elseif(isset($_SESSION['coupon'])){
	$sql = "SELECT * FROM coupons WHERE coupon_code=?";
	$result = $db->prepare($sql);
	$result->execute(array($_SESSION['coupon']));
	$count = $result->rowCount();
	$coupon = $result->fetch(PDO::FETCH_ASSOC);
}
?>
<!-- SHOP CONTENT -->
<section id="content">
	<div class="content-blog">
		<div class="container">
			<div class="row">
				<div class="page_header text-center">
					<h2>Shop Cart</h2>
					<p>Checkout these items to Place the Order</p>
				</div>
				<div class="col-md-12">
		<pre>
		<?php
			print_r($_SESSION);
		?>
		</pre>
		<table class="cart-table table table-bordered">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>Product</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				if(isset($_SESSION['cart'])){
					$cart = $_SESSION['cart'];
					$total = 0;
					foreach ($cart as $key => $value) {
						// key is id and value is qunatity
						$sql = "SELECT * FROM products WHERE id=?";
					    $result = $db->prepare($sql);
					    $result->execute(array($key));
					    $product = $result->fetch(PDO::FETCH_ASSOC);
			 ?>
				<tr>
					<td>
						<a href="del-cart.php?id=<?php echo $key; ?>" class="remove"><i class="fa fa-times"></i></a>
					</td>
					<td>
						<a href="product.php?id=<?php echo $product['id']; ?>"><img src="<?php echo $product['image']; ?>" alt="" height="90" width="90"></a>					
					</td>
					<td>
						<a href="product.php?id=<?php echo $product['id']; ?>"><?php echo $product['title']; ?></a>					
					</td>
					<td>
						<span class="amount">&#8377;<?php echo $product['price']; ?></span>					
					</td>
					<td>
						<div class="quantity"><?php echo $value['quantity']; ?></div>
					</td>
					<td>
						<span class="amount">&#8377;<?php echo ($product['price']*$value['quantity']); ?></span>					
					</td>
				</tr>
			<?php
				$total = $total + ($product['price']*$value['quantity']);
				} }else{ echo "<tr><td><h3>Add Products to Cart for Checkout.</h3></td></tr>"; } ?>
				<tr>
					<td colspan="6" class="actions">
						<div class="col-md-6">
							<div class="coupon">
								<form method="post">
									<label>Coupon:</label><br>
									<input name="coupon" placeholder="Coupon code" type="text" value="<?php if(isset($count)){ if($count == 1){ echo $coupon['coupon_code']; } } ?>"> <button type="submit">Apply</button>
								</form>
							<?php
			                    if(!empty($couponerrors)){
			                        echo "<div class='alert alert-danger'>";
			                        foreach ($couponerrors as $couponerror) {
			                            echo "<span class='glyphicon glyphicon-remove'></span>&nbsp;". $couponerror . "<br>";
			                        }
			                        echo "</div>";
			                    }
			                ?>
			                <small>
			                <?php
			                if(isset($count)){
			                	if($count == 1){
			                		echo "Coupon Type : " . $coupon['type'] . "<br>";
			                		if($coupon['type'] == 'percentage'){
			                			echo "Coupon Value : " . $coupon['coupon_value'] . "%<br>";
			                		}elseif($coupon['type'] == 'flat-rate'){
			                			echo "Coupon Value : &#8377;" . $coupon['coupon_value'] . "<br>";
			                		}
			                		
			                		echo "Description : " . $coupon['description'] . "<br>";
			                		echo "Terms : " . $coupon['terms'] . "<br>";
			                	}
			                }
			                ?>
			                </div>
			                </small>
						</div>
						<div class="col-md-6">
							<div class="cart-btn">
								<!-- <button class="button btn-md" type="submit">Update Cart</button> -->
								<a href="checkout.php" class="button btn-md" >Checkout</a>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>		

		<div class="cart_totals">
			<div class="col-md-6 push-md-6 no-padding">
				<h4 class="heading">Cart Totals</h4>
				<table class="table table-bordered col-md-6">
					<tbody>
						<tr>
							<th>Cart Subtotal</th>
							<td><span class="amount">&#8377;<?php if(!empty($total)){ echo $total; }else{ echo "0"; } ?></span></td>
						</tr>
						<tr>
							<th>Shipping and Handling</th>
							<td>
								Free Shipping				
							</td>
						</tr>
						<tr>
							<th>Discount</th>
							<td>
								&#8377; 50				
							</td>
						</tr>
						<tr>
							<th>Order Total</th>
							<td><strong><span class="amount">&#8377;<?php if(!empty($total)){ echo $total; }else{ echo "0"; } ?></span></strong> </td>
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
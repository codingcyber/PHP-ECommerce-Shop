<?php include('includes/header.php'); ?>
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
				<tr>
					<td>
						<a class="remove"><i class="fa fa-times"></i></a>
					</td>
					<td>
						<a href="#"><img src="images/shop/1.jpg" alt="" height="90" width="90"></a>					
					</td>
					<td>
						<a href="#">Product 1</a>					
					</td>
					<td>
						<span class="amount">&#8377;69.99</span>					
					</td>
					<td>
						<div class="quantity">1</div>
					</td>
					<td>
						<span class="amount">&#8377;69.99</span>					
					</td>
				</tr>
				<tr>
					<td>
						<a class="remove"><i class="fa fa-times"></i></a>
					</td>
					<td>
						<a href="#"><img src="images/shop/2.jpg" alt="" height="90" width="90"></a>					
					</td>
					<td>
						<a href="#">Product 2</a>					
					</td>
					<td>
						<span class="amount">&#8377;119.99</span>					
					</td>
					<td>
						<div class="quantity">1</div>
					</td>
					<td>
						<span class="amount">&#8377;119.99</span>					
					</td>
				</tr>
				<tr>
					<td colspan="6" class="actions">
						<div class="col-md-6">
							<div class="coupon">
								<label>Coupon:</label><br>
								<input placeholder="Coupon code" type="text"> <button type="submit">Apply</button>
							</div>
						</div>
						<div class="col-md-6">
							<div class="cart-btn">
								<button class="button btn-md" type="submit">Update Cart</button>
								<button class="button btn-md" type="submit">Checkout</button>
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
							<td><span class="amount">&#8377;190.00</span></td>
						</tr>
						<tr>
							<th>Shipping and Handling</th>
							<td>
								Free Shipping				
							</td>
						</tr>
						<tr>
							<th>Order Total</th>
							<td><strong><span class="amount">&#8377;190.00</span></strong> </td>
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
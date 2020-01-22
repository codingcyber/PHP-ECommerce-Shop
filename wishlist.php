<?php 
session_start();
require_once('includes/connect.php');
require_once('includes/check-login.php');
include('includes/header.php'); 
$sql = "SELECT * FROM wishlist WHERE uid=?";
$result = $db->prepare($sql);
$result->execute(array($_SESSION['id']));
$res = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<style type="text/css">
	.shop-mason-4col .sm-item{
		float: left;
	}
</style>
<!-- SHOP CONTENT -->
<section id="content">
	<div class="content-blog">
		<div class="container">
			<div class="row">
				<div class="page_header text-center">
					<h2>Your Wishlist</h2>
					<p>Products You Want to Buy!</p>
				</div>
				<div class="col-md-12">
					<div class="row">
						<div id="shop-mason" class="shop-mason-4col">
							<?php 
								// TODO : Add Pagination
								// Only show Published products
							foreach ($res as $r) {
								$sql = "SELECT * FROM products WHERE id=?";
                                $result = $db->prepare($sql);
                                $result->execute(array($r['pid']));
                                $product = $result->fetch(PDO::FETCH_ASSOC);
							 ?>
							<div class="sm-item isotope-item">
								<div class="product">
									<div class="product-thumb">
										<img src="<?php echo $product['image']; ?>" class="img-responsive" alt="">
										<div class="product-overlay">
											<span>
											<a href="product.php?id=<?php echo $product['id']; ?>" class="fa fa-link"></a>
											<a href="add-to-cart.php?id=<?php echo $product['id']; ?>" class="fa fa-shopping-cart"></a>
											</span>					
										</div>
									</div>
									<div class="rating">
										<span class="fa fa-star act"></span>
										<span class="fa fa-star act"></span>
										<span class="fa fa-star act"></span>
										<span class="fa fa-star act"></span>
										<span class="fa fa-star act"></span>
									</div>
									<h2 class="product-title"><a href="#"><?php echo $product['title']; ?></a></h2>
									<div class="product-price">&#8377;<?php echo $product['price']; ?></div>
								</div>
							</div>
							<?php } ?>
							
						</div>
					</div>
					<div class="clearfix"></div>
					<!-- Pagination -->
					<div class="page_nav">
						<a href=""><i class="fa fa-angle-left"></i></a>
						<a href="" class="active">1</a>
						<a href="">2</a>
						<a href="">3</a>
						<a class="no-active">...</a>
						<a href="">9</a>
						<a href=""><i class="fa fa-angle-right"></i></a>
					</div>
					<!-- End Pagination -->
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('includes/footer.php'); ?>
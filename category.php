<?php 
require_once('includes/connect.php');
if(isset($_GET['id']) & !empty($_GET['id'])){
	// Fetching Category Name
	$sql = "SELECT * FROM categories WHERE id=?";
    $result = $db->prepare($sql);
    $result->execute(array($_GET['id']));
    $cat = $result->fetch(PDO::FETCH_ASSOC);

    // Fetch Product id's based on category id from product_categories
    $catsql = "SELECT * FROM product_categories WHERE cid=?";
    $catresult = $db->prepare($catsql);
    $catresult->execute(array($_GET['id']));
    $productcategories = $catresult->fetchAll(PDO::FETCH_ASSOC);
    $catcount = $catresult->rowCount();
}else{
	header('location: index.php');
}
include('includes/header.php'); 
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
					<h2>Shop: <?php echo $cat['title']; ?></h2>
					<p><?php echo $cat['description']; ?></p>
				</div>
				<div class="col-md-12">
					<div class="row">
						<div id="shop-mason" class="shop-mason-4col">
							<?php 
								if($catcount > 1){
									foreach ($productcategories as $catid) {
									// TODO : Add Pagination
									// Only show Published products
									$sql = "SELECT * FROM products WHERE id=?";
                                    $result = $db->prepare($sql);
                                    $result->execute(array($catid['pid']));
                                    $products = $result->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($products as $product) {
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
							<?php } } } ?>
							
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
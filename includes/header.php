<!DOCTYPE html>
<!--[if IE 8]>			<html class="ie ie8"> <![endif]-->
<!--[if IE 9]>			<html class="ie ie9"> <![endif]-->
<!--[if gt IE 9]><!-->	<html> <!--<![endif]-->
<head>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta name="keywords" content="HTML5 Template" />
	<meta name="description" content="">
	<meta name="author" content="">

	<title>E-commerce Shop Home Page</title>

	<!-- Mobile Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Favicon -->
	<link rel="shortcut icon" href="images/favicon.png">

	<!-- CSS -->
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="js/superfish/css/superfish.css" media="screen">
	<link rel="stylesheet" href="css/style.css">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<!-- Modernizer -->
	<script src="js/modernizr.custom.js"></script>

</head>
<body class="multi-page">

<div id="wrapper" class="wrapper">
	<!-- HEADER -->
	<header id="header2">
		<div class="container">
			<div class="row">
				<div class="col-md-3 col-xs-5 logo">
					<a href="index.php"><img src="https://codingcyber.org/wp-content/uploads/2017/09/logo.png" class="img-responsive" alt=""/></a>
				</div>
				<div class="col-md-9 col-xs-7">
					<div class="top-bar">
						
					</div>
				</div>
			</div>
			<div class="menu-wrap">
				<div id="mobnav-btn">Menu <i class="fa fa-bars"></i></div>
				<ul class="sf-menu">
					<li>
						<a href="index.php">Home</a>
					</li>
					<li>
						<a href="#">Shop</a>
						<div class="mobnav-subarrow"><i class="fa fa-plus"></i></div>
						<ul>
							<?php 
								$sql = "SELECT * FROM categories";
							    $result = $db->prepare($sql);
							    $result->execute();
							    $categories = $result->fetchAll(PDO::FETCH_ASSOC);
							    foreach ($categories as $category) {
							 ?>
							<li><a href="category.php?id=<?php echo $category['id']; ?>"><?php echo $category['title']; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					<?php 
						if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
							$sql = "SELECT * FROM users WHERE id=?";
							$result = $db->prepare($sql);
							$result->execute(array($_SESSION['id']));
							$user = $result->fetch(PDO::FETCH_ASSOC);
							if($user['role'] == 'customer'){
					 ?>
					<li>
						<a href="#">My Account</a>
						<div class="mobnav-subarrow"><i class="fa fa-plus"></i></div>
						<ul>
							<li><a href="my-account.php">My Account</a></li>
							<li><a href="wishlist.php">My Wishlist</a></li>
							<li><a href="logout.php">Logout</a></li>
						</ul>
					</li>
					<?php } }else{ ?>
					<li>
						<a href="login.php">Login / Register</a>
					</li>
					<?php } ?>
					<li>
						<a href="#">Contact</a>
					</li>
				</ul>
				<div class="header-xtra">
					<?php 
						if(isset($_SESSION['cart'])){
							$cart = $_SESSION['cart'];
							$total = 0;
					 ?>
					<div class="s-cart">
						<div class="sc-ico"><i class="fa fa-shopping-cart"></i><em><?php echo count($cart); ?></em></div>

						<div class="cart-info">
							<small>You have <em class="highlight"><?php echo count($cart); ?> item(s)</em> in your shopping bag</small>
							<br>
							<br>
							<?php 
								$total = 0;
								foreach ($cart as $key => $value) {
								// key is id and value is qunatity
								$sql = "SELECT * FROM products WHERE id=?";
							    $result = $db->prepare($sql);
							    $result->execute(array($key));
							    $prod = $result->fetch(PDO::FETCH_ASSOC);
							 ?>
							<div class="ci-item">
								<img src="<?php echo $prod['image']; ?>" width="70" alt=""/>
								<div class="ci-item-info">
									<h5><a href="product.php?id=<?php echo $prod['id']; ?>"><?php echo $prod['title']; ?></a></h5>
									<p><?php echo $value['quantity']; ?> x &#8377;<?php echo $prod['price']; ?></p>
									<div class="ci-edit">
										<!-- <a href="#" class="edit fa fa-edit"></a> -->
										<a href="del-cart.php?id=<?php echo $key; ?>" class="edit fa fa-trash"></a>
									</div>
								</div>
							</div>
							<?php 
							$total = $total + ($prod['price']*$value['quantity']);
						} ?>
							<div class="ci-total">Subtotal: &#8377;<?php if(!empty($total)){ echo $total; }else{ echo "0"; } ?></div>
							<div class="cart-btn">
								<a href="cart.php">View Bag</a>
								<a href="checkout.php">Checkout</a>
							</div>
						</div>
					</div>
					<?php } ?>
					<div class="s-search">
						<div class="ss-ico"><i class="fa fa-search"></i></div>
						<div class="search-block">
							<div class="ssc-inner">
								<form>
									<input type="text" placeholder="Type Search text here...">
									<button type="submit"><i class="fa fa-search"></i></button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	
	<div class="close-btn fa fa-times"></div>
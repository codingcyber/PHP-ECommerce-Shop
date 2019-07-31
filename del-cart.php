<?php 
session_start();
if (isset($_GET['id']) & !empty($_GET['id'])) {
	$id = $_GET['id'];
	unset($_SESSION['cart'][$id]);
	header('location: cart.php');
}else{
	header('location: cart.php');
}
echo "<pre>";
print_r($_SESSION['cart']);
echo "</pre>";
?>
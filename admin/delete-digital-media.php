<?php 
require_once('../includes/connect.php');
include('includes/check-login.php');
if(isset($_GET) & !empty($_GET)){
	$id = $_GET['id'];
	$sql = "SELECT * FROM product_digital WHERE pid=?";
	$result = $db->prepare($sql);
	$result->execute(array($_GET['id']));
	$product = $result->fetch(PDO::FETCH_ASSOC);
	$filepath = '../'.$product['media'];
	if(file_exists($filepath)){
		if(unlink($filepath)){
			$sql = "DELETE FROM product_digital WHERE pid=?";
			$result = $db->prepare($sql);
			$res = $result->execute(array($_GET['id']));
			if($res){
				$redirect = 'manage-digital-product.php?id='.$_GET['id'];
				header("location: $redirect");
			}
		}
	}else{
		$sql = "DELETE FROM product_digital WHERE pid=?";
		$result = $db->prepare($sql);
		$res = $result->execute(array($_GET['id']));
		if($res){
			$redirect = 'manage-digital-product.php?id='.$_GET['id'];
			header("location: $redirect");
		}
	}
}
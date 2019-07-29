<?php 
require_once('../includes/connect.php');
include('includes/check-login.php');
if(isset($_GET) & !empty($_GET)){
	$id = $_GET['id'];
	$sql = "SELECT * FROM products WHERE id=?";
	$result = $db->prepare($sql);
	$result->execute(array($_GET['id']));
	$product = $result->fetch(PDO::FETCH_ASSOC);
	$filepath = '../'.$product['image'];
	if(file_exists($filepath)){
		if(unlink($filepath)){
			$sql = "UPDATE products SET image='', updated=NOW() WHERE id=?";
			$result = $db->prepare($sql);
			$res = $result->execute(array($_GET['id']));
			if($res){
				$redirect = 'edit-product.php?id='.$_GET['id'];
				header("location: $redirect");
			}
		}
	}else{
		$sql = "UPDATE products SET image='', updated=NOW() WHERE id=?";
		$result = $db->prepare($sql);
		$res = $result->execute(array($_GET['id']));
		if($res){
			$redirect = 'edit-product.php?id='.$_GET['id'];
			header("location: $redirect");
		}
	}
}
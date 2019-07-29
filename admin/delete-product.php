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
			$sql = "DELETE FROM products WHERE id=?";
			$result = $db->prepare($sql);
			$res = $result->execute(array($_GET['id']));
			if($res){
				header("location: view-products.php");
			}
		}
	}else{
		$sql = "DELETE FROM products WHERE id=?";
		$result = $db->prepare($sql);
		$res = $result->execute(array($_GET['id']));
		if($res){
			header("location: view-products.php");
		}
	}
}
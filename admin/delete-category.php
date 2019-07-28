<?php 
require_once('../includes/connect.php');
include('includes/check-login.php');
$sql = "DELETE FROM categories WHERE id=?";
$result = $db->prepare($sql);
$res = $result->execute(array($_GET['id']));
if($res){
	header('location: view-categories.php');
}
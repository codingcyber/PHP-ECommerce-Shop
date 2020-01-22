<?php
session_start();
require_once('includes/connect.php');
require_once('includes/check-login.php');
// select sql query with pid & uid, if the number of rows are 1
$sql = "SELECT * FROM wishlist WHERE pid=:pid AND uid=:uid";
$result = $db->prepare($sql);
$values = array(':pid'     		=> $_GET['id'],
                ':uid'     		=> $_SESSION['id']
                );
$result->execute($values);
$count = $result->rowCount();
if($count < 1){ 
	$sql = "INSERT INTO wishlist (pid, uid) VALUES (:pid, :uid)";
	$result = $db->prepare($sql);
	$values = array(':pid'     		=> $_GET['id'],
	                ':uid'     		=> $_SESSION['id']
	                );
	$res = $result->execute($values) or die(print_r($result->errorInfo(), true));
	echo "Item added to wishlist";
}else{
	echo "Failed to add item to wishlist";
}
header('location: wishlist.php');

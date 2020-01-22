<?php
session_start();
require_once('includes/connect.php');
require_once('includes/check-login.php');
$sql = "DELETE FROM wishlist WHERE uid=:uid AND pid=:pid";
$result = $db->prepare($sql);
$values = array(':pid'     		=> $_GET['id'],
                ':uid'     		=> $_SESSION['id']
                );
$res = $result->execute($values);
header("location: wishlist.php");
<?php
session_start();
unset($_SESSION['coupon']);
header("location: cart.php");
?>
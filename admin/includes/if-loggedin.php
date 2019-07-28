<?php
// check the page
//echo basename($_SERVER['PHP_SELF']);
if(basename($_SERVER['PHP_SELF']) == 'login.php'){
	if(isset($_SESSION['id']) & !empty($_SESSION['id'])){
		// redirect to dashboard page
		header('location: http://localhost/PHP-ECommerce-Shop/admin/dashboard.php');
		$sql = "SELECT * FROM users WHERE id=?";
		$result = $db->prepare($sql);
		$result->execute(array($_SESSION['id']));
		$user = $result->fetch(PDO::FETCH_ASSOC);
		if($user['role'] == 'admin'){
			// redirect to admin dashboard page
			header('location: http://localhost/PHP-ECommerce-Shop/admin/dashboard.php');
		}else{
			// redirect to shop home page
			header('location: http://localhost/PHP-ECommerce-Shop/index.php');
		}
	}
}
?>
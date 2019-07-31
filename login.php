<?php 
session_start();
require_once('includes/connect.php');
// If LoggedIn Redirect to checkout page 
if(isset($_POST) & !empty($_POST)){
	// ***********
	// Login *****
	// ***********
	if($_POST['submit'] == 'Login'){
		// PHP Form Validations
	    if(empty($_POST['username'])){ $logerrors[]="User Name / E-Mail field is Required"; }
	    if(empty($_POST['password'])){ $logerrors[]="Password field is Required"; }

	    // CSRF Token Validation
	    if(isset($_POST['csrf_token'])){
	        if($_POST['csrf_token'] === $_SESSION['csrf_token']){
	        }else{
	            $logerrors[] = "Problem with CSRF Token Validation";
	        }
	    }
	    // CSRF Token Time Validation
	    $max_time = 60*60*24; // in seconds
	    if(isset($_SESSION['csrf_token_time'])){
	        $token_time = $_SESSION['csrf_token_time'];
	        if(($token_time + $max_time) >= time() ){
	        }else{
	            $logerrors[] = "CSRF Token Expired";
	            unset($_SESSION['csrf_token']);
	            unset($_SESSION['csrf_token_time']);
	        }
	    }

	    if(empty($errors)){
	        // Check the Login Credentials
	        $sql = "SELECT * FROM users WHERE ";
	        if(filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)){
	            $sql .= "email=?";
	        }else{
	            $sql .= "username=?";
	        }
	        $sql .= " AND role='customer'";
	        $result = $db->prepare($sql);
	        $result->execute(array($_POST['username']));
	        $count = $result->rowCount();
	        $res = $result->fetch(PDO::FETCH_ASSOC);
	        if($count == 1){
	            // Compare the password with password hash
	            if(password_verify($_POST['password'], $res['password'])){
	                // regenerate session id
	                session_regenerate_id();
	                $_SESSION['login'] = true;
	                $_SESSION['id'] = $res['id'];
	                $_SESSION['last_login'] = time();
	                // redirect the user to checkout page
	                header("location: checkout.php");
	            }else{
	                $errors[] = "User Name / E-Mail & Password Combination not Working";
	            }
	        }else{
	            $errors[] = "User Name / E-Mail not Valid";
	        }
	    }
	}

	// ***********
	// Register***
	// ***********
	if($_POST['submit'] == 'Register'){
		// PHP Form Validations
	    if(empty($_POST['uname'])){ $regerrors[]="User Name field is Required"; }else{
	        // Check Username is Unique with DB query
	        $sql = "SELECT * FROM users WHERE username=?";
	        $result = $db->prepare($sql);
	        $result->execute(array($_POST['uname']));
	        $count = $result->rowCount();
	        if($count == 1){
	            $regerrors[] = "User Name already exists in database";
	        }
	    }
	    if(empty($_POST['email'])){ $regerrors[]="E-mail field is Required"; }else{
	        // Check Email is Unique with DB Query
	        $sql = "SELECT * FROM users WHERE email=?";
	        $result = $db->prepare($sql);
	        $result->execute(array($_POST['email']));
	        $count = $result->rowCount();
	        if($count == 1){
	            $regerrors[] = "E-Mail already exists in database";
	        }
	    }

	    if(empty($_POST['password'])){ $regerrors[]="Password field is Required"; }else{
	        // check the repeat password
	        if(empty($_POST['passwordr'])){ $regerrors[]="Repeat Password field is Required"; }else{
	            // compare both passwords, if they match. Generate the Password Hash
	            if($_POST['password'] == $_POST['passwordr']){
	                // create password hash
	                $pass_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
	            }else{
	                // Display Error Message
	                $regerrors[] = "Both Passwords Should Match";
	            }
	        }
	    }
		// CSRF Token Validation
	    if(isset($_POST['csrf_token'])){
	        if($_POST['csrf_token'] === $_SESSION['csrf_token']){
	        }else{
	            $regerrors[] = "Problem with CSRF Token Validation";
	        }
	    }
	    // CSRF Token Time Validation
	    $max_time = 60*60*24; // in seconds
	    if(isset($_SESSION['csrf_token_time'])){
	        $token_time = $_SESSION['csrf_token_time'];
	        if(($token_time + $max_time) >= time() ){
	        }else{
	            $regerrors[] = "CSRF Token Expired";
	            unset($_SESSION['csrf_token']);
	            unset($_SESSION['csrf_token_time']);
	        }
	    }

	    if(empty($regerrors)){
	    	// Insert the submistted details into users database with customer role
	    	$sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'customer')";
	        $result = $db->prepare($sql);
	        $values = array(':username'     => $_POST['uname'],
	                        ':email'        => $_POST['email'],
	                        ':password'     => $pass_hash
	                        );
	        $res = $result->execute($values) or die(print_r($result->errorInfo(), true));
	        if($res){
	        	// create the session and redirect to checkout page
	        	// regenerate session id
                session_regenerate_id();
                $_SESSION['login'] = true;
                $_SESSION['id'] = $res['id'];
                $_SESSION['last_login'] = time();
	        }
	    }
	}
}
// Create CSRF token
$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time'] = time();
include('includes/header.php');
?>
<!-- SHOP CONTENT -->
<section id="content">
	<div class="content-blog">
		<div class="container">
			<div class="row">
				<div class="page_header text-center">
					<h2>Shop - Account</h2>
					<p>Login to Your Account</p>
				</div>
				<div class="col-md-12">
			<div class="row shop-login">
			<div class="col-md-6">
				<div class="box-content">
					<h3 class="heading text-center">I'm a Returning Customer</h3>
					<div class="clearfix space40"></div>
					<?php
                        if(!empty($logerrors)){
                            echo "<div class='alert alert-danger'>";
                            foreach ($logerrors as $logerror) {
                                echo "&nbsp;".$logerror."<br>";
                            }
                            echo "</div>";
                        }
                    ?>
					<form class="logregform" method="post">
						<input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
						<div class="row">
							<div class="form-group">
								<div class="col-md-12">
									<label>Username or E-mail Address</label>
									<input type="text" name="username" class="form-control" value="<?php if(isset($_POST['username'])){ echo $_POST['username']; } ?>">
								</div>
							</div>
						</div>
						<div class="clearfix space20"></div>
						<div class="row">
							<div class="form-group">
								<div class="col-md-12">
									<a class="pull-right" href="#">(Lost Password?)</a>
									<label>Password</label>
									<input type="password" name="password" value="" class="form-control">
								</div>
							</div>
						</div>
						<div class="clearfix space20"></div>
						<div class="row">
							<div class="col-md-6">
								<!-- <span class="remember-box checkbox">
								<label for="rememberme">
								<input type="checkbox" id="rememberme" name="rememberme">Remember Me
								</label>
								</span> -->
							</div>
							<div class="col-md-6">
								<input type="submit" name="submit" class="button btn-md pull-right" value="Login" />
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="col-md-6">
				<div class="box-content">
					<h3 class="heading text-center">Register An Account</h3>
					<div class="clearfix space40"></div>
					<?php
                        if(!empty($regerrors)){
                            echo "<div class='alert alert-danger'>";
                            foreach ($regerrors as $regerror) {
                                echo "&nbsp;".$regerror."<br>";
                            }
                            echo "</div>";
                        }
                    ?>
					<form class="logregform" method="post">
						<input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
						<div class="row">
							<div class="form-group">
								<div class="col-md-12">
									<label>User Name</label>
									<input type="text" name="uname" class="form-control" value="<?php if(isset($_POST['uname'])){ echo $_POST['uname']; } ?>">
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12">
									<label>E-mail Address</label>
									<input type="email" name="email" class="form-control" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } ?>">
								</div>
							</div>
						</div>
						<div class="clearfix space20"></div>
						<div class="row">
							<div class="form-group">
								<div class="col-md-6">
									<label>Password</label>
									<input type="password" name="password" value="" class="form-control">
								</div>
								<div class="col-md-6">
									<label>Re-enter Password</label>
									<input type="password" name="passwordr" value="" class="form-control">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="space20"></div>
								<input type="submit" name="submit" class="button btn-md pull-right" value="Register" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>


						
				</div>
			</div>
		</div>
	</div>
</section>
<?php include('includes/footer.php'); ?>
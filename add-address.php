<?php 
session_start();
require_once('includes/connect.php');
// check user login - customer
require_once('includes/check-login.php');
if(isset($_POST) & !empty($_POST)){
	// PHP Form Validations
	if(empty($_POST['nickname'])){ $errors[]="Address Nick Name field is Required"; }
	if(empty($_POST['fname'])){ $errors[]="First Name field is Required"; }
	if(empty($_POST['lname'])){ $errors[]="Last Name field is Required"; }
	if(empty($_POST['address1'])){ $errors[]="Address Line 1 field is Required"; }
	if(empty($_POST['city'])){ $errors[]="City field is Required"; }
	if(empty($_POST['state'])){ $errors[]="State field is Required"; }
	if(empty($_POST['zipcode'])){ $errors[]="Zip Code field is Required"; }
	if(empty($_POST['phone'])){ $errors[]="Phone Number field is Required"; }

	// CSRF Token Validation
    if(isset($_POST['csrf_token'])){
        if($_POST['csrf_token'] === $_SESSION['csrf_token']){
        }else{
            $errors[] = "Problem with CSRF Token Validation";
        }
    }
    // CSRF Token Time Validation
    $max_time = 60*60*24; // in seconds
    if(isset($_SESSION['csrf_token_time'])){
        $token_time = $_SESSION['csrf_token_time'];
        if(($token_time + $max_time) >= time() ){
        }else{
            $errors[] = "CSRF Token Expired";
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
        }
    }

    if(empty($regerrors)){
    	// Insert the submitted details into users database with customer role
    	$sql = "INSERT INTO user_address (uid, nickname, fname, lname, address1, address2, city, state, country, zipcode, phone) VALUES (:uid, :nickname, :fname, :lname, :address1, :address2, :city, :state, :country, :zipcode, :phone)";
        $result = $db->prepare($sql);
        $values = array(':uid'     		=> $_SESSION['id'],
                        ':nickname'     => $_POST['nickname'],
                        ':fname'        => $_POST['fname'],
                        ':lname'        => $_POST['lname'],
                        ':address1'     => $_POST['address1'],
                        ':address2'     => $_POST['address2'],
                        ':city'        	=> $_POST['city'],
                        ':state'        => $_POST['state'],
                        ':country'      => $_POST['country'],
                        ':zipcode'      => $_POST['zipcode'],
                        ':phone'        => $_POST['phone']
                        );
        $res = $result->execute($values) or die(print_r($result->errorInfo(), true));
        if($res){
        	echo "Address Added";
        	// redirect to My Account Page
        	header('location: my-account.php');
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
		<div class="page_header text-center">
			<h2>Shop - Add Address</h2>
			<p>Add Your Address Here</p>
		</div>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="billing-details">
					<?php
                        if(!empty($errors)){
                            echo "<div class='alert alert-danger'>";
                            foreach ($errors as $error) {
                                echo "&nbsp;".$error."<br>";
                            }
                            echo "</div>";
                        }
                    ?>
					<h3 class="uppercase">Billing Details</h3>
					<div class="space30"></div>
					<form method="post">
						<input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
						<label>Address Nick Name</label>
						<input name="nickname" class="form-control" placeholder="" type="text" value="<?php if(isset($_POST['nickname'])){ echo $_POST['nickname']; } ?>">
						<div class="clearfix space20"></div>
						<label class="">Country </label>
						<select name="country" class="form-control">
							<option value="">Select Country</option>
							<option value="AX">Aland Islands</option>
							<option value="AF">Afghanistan</option>
							<option value="AL">Albania</option>
							<option value="DZ">Algeria</option>
							<option value="AD">Andorra</option>
							<option value="AO">Angola</option>
							<option value="AI">Anguilla</option>
							<option value="AQ">Antarctica</option>
							<option value="AG">Antigua and Barbuda</option>
							<option value="AR">Argentina</option>
							<option value="AM">Armenia</option>
							<option value="AW">Aruba</option>
							<option value="AU">Australia</option>
							<option value="AT">Austria</option>
							<option value="AZ">Azerbaijan</option>
							<option value="BS">Bahamas</option>
							<option value="BH">Bahrain</option>
							<option value="BD">Bangladesh</option>
							<option value="BB">Barbados</option>
						</select>
						<div class="clearfix space20"></div>
						<div class="row">
							<div class="col-md-6">
								<label>First Name </label>
								<input name="fname" class="form-control" placeholder="" type="text" value="<?php if(isset($_POST['fname'])){ echo $_POST['fname']; } ?>">
							</div>
							<div class="col-md-6">
								<label>Last Name </label>
								<input name="lname" class="form-control" placeholder="" type="text" value="<?php if(isset($_POST['lname'])){ echo $_POST['lname']; } ?>">
							</div>
						</div>
						<div class="clearfix space20"></div>
						<label>Address </label>
						<input name="address1" class="form-control" placeholder="Street address" type="text" value="<?php if(isset($_POST['address1'])){ echo $_POST['address1']; } ?>">
						<div class="clearfix space20"></div>
						<input name="address2" class="form-control" placeholder="Apartment, suite, unit etc. (optional)" type="text" value="<?php if(isset($_POST['address2'])){ echo $_POST['address2']; } ?>">
						<div class="clearfix space20"></div>
						<div class="row">
							<div class="col-md-4">
								<label>Town / City </label>
								<input name="city" class="form-control" placeholder="Town / City" type="text" value="<?php if(isset($_POST['city'])){ echo $_POST['city']; } ?>">
							</div>
							<div class="col-md-4">
								<label>State</label>
								<input name="state" class="form-control" placeholder="State / County" type="text" value="<?php if(isset($_POST['state'])){ echo $_POST['state']; } ?>">
							</div>
							<div class="col-md-4">
								<label>Postcode </label>
								<input name="zipcode" class="form-control" placeholder="Postcode / Zip" type="text" value="<?php if(isset($_POST['zipcode'])){ echo $_POST['zipcode']; } ?>">
							</div>
						</div>
						<div class="clearfix space20"></div>
						<label>Phone Number</label>
						<input name="phone" class="form-control" placeholder="Phone Number" type="number" value="<?php if(isset($_POST['phone'])){ echo $_POST['phone']; } ?>">
						<div class="clearfix space20"></div>
						<input type="submit" value="Submit" class="button btn-lg" />
					</form>
				</div>
			</div>
		</div>
	</div>		
	
	</div>
</section>
<?php include('includes/footer.php'); ?>
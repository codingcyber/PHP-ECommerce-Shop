<?php 
require_once('../includes/connect.php');
include('includes/check-login.php'); 
if(isset($_POST) & !empty($_POST)){
    // PHP Form Validations
    if(empty($_POST['code'])){ $errors[] = "Coupon Code is Required";}else{
        // check the coupon code in unique
        $sql = "SELECT * FROM coupons WHERE coupon_code=?";
        $result = $db->prepare($sql);
        $result->execute(array($_POST['code']));
        $count = $result->rowCount();
        if($count == 1){
            $errors[] = 'Coupon Code already exists in database';
        }
    }
    if(empty($_POST['type'])){ $errors[] = "Coupon Type is Required";}
    if(empty($_POST['discount'])){ $errors[] = "Coupon Discount Value is Required";}
    if(empty($_POST['limit'])){ $errors[] = "Coupon Limit is Required";}
    if(empty($_POST['expiry'])){ $errors[] = "Coupon Expiry Date is Required";}

    // CSRF Token Validation
    if(isset($_POST['csrf_token'])){
        if($_POST['csrf_token'] == $_SESSION['csrf_token']){
        }else{
            $errors[] = "Problem with CSRF Token Verification";
        }
    }else{
        $errors[] = "Problem with CSRF Token Validation";
    }
    // CSRF Token Time Validation
    $max_time = 60*60*24;
    if(isset($_SESSION['csrf_token_time'])){
        $token_time = $_SESSION['csrf_token_time'];
        if(($token_time + $max_time) >= time()){
        }else{
            $errors[] = "CSRF Token Expired";
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
        }
    }else{
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
    }

    if(empty($errors)){
        // Insert into categories table
        $sql = "INSERT INTO coupons (coupon_code, type, description, terms, coupon_value, coupon_limit, coupon_expiry) VALUES (:coupon_code, :type, :description, :terms, :coupon_value, :coupon_limit, :coupon_expiry)";
        $result = $db->prepare($sql);
        $values = array(':coupon_code'      => strtoupper($_POST['code']),
                        ':type'             => $_POST['type'],
                        ':description'      => $_POST['description'],
                        ':terms'            => $_POST['terms'],
                        ':coupon_value'     => $_POST['discount'],
                        ':coupon_limit'     => $_POST['limit'],
                        ':coupon_expiry'    => $_POST['expiry']
                        );
        $res = $result->execute($values);
        if($res){
            header('location: view-coupons.php');
        }else{
            $errors[] = "Failed to Add Coupon";
        }
    }
}
// Create CSRF Token
$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time']    = time();
include('includes/header.php');
include('includes/navigation.php');
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add Coupons</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Create a New Coupon Here...
                </div>
                <div class="panel-body">
                    <?php
                        if(!empty($errors)){
                            echo "<div class='alert alert-danger'>";
                            foreach ($errors as $error) {
                                echo "<span class='glyphicon glyphicon-remove'></span>&nbsp;". $error . "<br>";
                            }
                            echo "</div>";
                        }
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <form role="form" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                <div class="form-group">
                                    <label>Coupon Code</label>
                                    <input name="code" class="form-control" placeholder="Enter Category Title" value="<?php if(isset($_POST['code'])){ echo $_POST['code']; } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Coupon Description</label>
                                    <textarea name="description" class="form-control" rows="3"><?php if(isset($_POST['description'])){ echo $_POST['description']; } ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Terms</label>
                                    <textarea name="terms" class="form-control" rows="3"><?php if(isset($_POST['terms'])){ echo $_POST['terms']; } ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Type</label>
                                    <select name="type" class="form-control">
                                        <option value="">--Select Coupon Type--</option>
                                        <option value="flat-rate">Flat Rate</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Category Discount Value</label>
                                    <input name="discount" type="number" class="form-control" placeholder="Enter Coupon Discount Value" value="<?php if(isset($slug)){ echo $slug; } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Coupon Limit</label>
                                    <input name="limit" type="number" class="form-control" placeholder="Enter Coupon Limit" value="<?php if(isset($slug)){ echo $slug; } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Coupon Expiry (2018-10-30)</label>
                                    <input name="expiry" class="form-control" placeholder="Enter Coupon Expiry Day" value="<?php if(isset($slug)){ echo $slug; } ?>">
                                </div>

                                <input type="submit" class="btn btn-primary" value="Submit" />
                            </form>
                        </div>
                        <!-- /.col-lg-6 (nested) -->   
                    <!-- /.row (nested) -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<?php include('includes/footer.php'); ?>
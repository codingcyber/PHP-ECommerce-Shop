<?php 
require_once('../includes/connect.php');
include('includes/check-login.php'); 
if(isset($_POST) & !empty($_POST)){
    // PHP Form Validations
    
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
        $sql = "UPDATE coupons SET description=:description, terms=:terms, coupon_limit=:coupon_limit, coupon_expiry=:coupon_expiry, updated=NOW() WHERE id=:id";
        $result = $db->prepare($sql);
        $values = array(':description'      => $_POST['description'],
                        ':terms'            => $_POST['terms'],
                        ':coupon_limit'     => $_POST['limit'],
                        ':coupon_expiry'    => $_POST['expiry'],
                        ':id'               => $_POST['id']
                        );
        $res = $result->execute($values);
        if($res){
            header('location: view-coupons.php');
        }else{
            $errors[] = "Failed to Update Coupon";
        }
    }
}
// Create CSRF Token
$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time']    = time();

$sql = "SELECT * FROM coupons WHERE id=?";
$result = $db->prepare($sql);
$result->execute(array($_GET['id']));
$coupon = $result->fetch(PDO::FETCH_ASSOC);

include('includes/header.php');
include('includes/navigation.php');
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Update Coupon</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Update Coupon Here...
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
                                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                <div class="form-group">
                                    <label>Coupon Code</label>
                                    <input name="code" class="form-control" placeholder="Enter Category Title" value="<?php if(isset($coupon['coupon_code'])){ echo $coupon['coupon_code']; } ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Description</label>
                                    <textarea name="description" class="form-control" rows="3"><?php if(isset($coupon['description'])){ echo $coupon['description']; } ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Terms</label>
                                    <textarea name="terms" class="form-control" rows="3"><?php if(isset($coupon['terms'])){ echo $coupon['terms']; } ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Type</label>
                                    <select name="type" class="form-control" disabled>
                                        <option value="">--Select Coupon Type--</option>
                                        <option value="flat-rate" <?php if($coupon['type'] == 'flat-rate'){ echo "selected"; } ?>>Flat Rate</option>
                                        <option value="percentage" <?php if($coupon['type'] == 'percentage'){ echo "selected"; } ?>>Percentage</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Category Discount Value</label>
                                    <input name="discount" type="number" class="form-control" placeholder="Enter Coupon Discount Value" value="<?php if(isset($coupon['coupon_value'])){ echo $coupon['coupon_value']; } ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Coupon Limit</label>
                                    <input name="limit" type="number" class="form-control" placeholder="Enter Coupon Limit" value="<?php if(isset($coupon['coupon_limit'])){ echo $coupon['coupon_limit']; } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Coupon Expiry (2018-10-30)</label>
                                    <input name="expiry" class="form-control" placeholder="Enter Coupon Expiry Day" value="<?php if(isset($coupon['coupon_expiry'])){ echo $coupon['coupon_expiry']; } ?>">
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
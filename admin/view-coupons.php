<?php 
require_once('../includes/connect.php');
include('includes/check-login.php');
include('includes/header.php');
include('includes/navigation.php'); 

// number of results per page
$perpage = 2;
if(isset($_GET['page']) & !empty($_GET['page'])){
    $curpage = $_GET['page'];
}else{
    $curpage = 1;
}

// get the number of total coupons table
$sql = "SELECT * FROM coupons";
$result = $db->prepare($sql);
$result->execute();
$totalres = $result->rowCount();

// calculate startpage, nextpage, endpage variables
$endpage = ceil($totalres/$perpage);
$startpage = 1;
$nextpage = $curpage + 1;
$previouspage = $curpage - 1;
$start = ($curpage * $perpage) - $perpage;

?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">View Coupons</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    View All the Coupons Here... 
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Coupon Code</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Limit</th>
                                    <th>Expiry</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $sql = "SELECT * FROM coupons LIMIT $start, $perpage";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $res = $result->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($res as $coupon) {
                                 ?>
                                <tr>
                                    <td><?php echo $coupon['id']; ?></td>
                                    <td><?php echo $coupon['coupon_code']; ?></td>
                                    <td><?php echo $coupon['type']; ?></td>
                                    <td><?php echo $coupon['coupon_value']; ?></td>
                                    <td><?php echo $coupon['coupon_limit']; ?></td>
                                    <td><?php echo $coupon['coupon_expiry']; ?></td>
                                    <td><a href="edit-coupon.php?id=<?php echo $coupon['id']; ?>">Edit</a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                    <ul class="pagination justify-content-center mb-4">
                        <?php if($curpage != $startpage){ ?>
                        <li class="page-item">
                            <a href="?page=<?php echo $startpage; ?>" class="page-link">&laquo; First</a>
                        </li>
                        <?php } ?>
                        <?php if($curpage >= 2){ ?>
                        <li class="page-item">
                            <a href="?page=<?php echo $previouspage; ?>" class="page-link"><?php echo $previouspage; ?></a>
                        </li>
                        <?php } ?>
                        <?php if($curpage != $endpage){ ?>
                        <li class="page-item">
                            <a href="?page=<?php echo $nextpage; ?>" class="page-link"><?php echo $nextpage; ?></a>
                        </li>
                        <?php } ?>
                        <?php if($curpage != $endpage){ ?>
                        <li class="page-item">
                            <a href="?page=<?php echo $endpage; ?>" class="page-link">&raquo; Last</a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <!-- /.panel-body -->
            </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<?php include('includes/footer.php'); ?>
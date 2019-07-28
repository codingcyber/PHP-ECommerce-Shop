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

// get the number of total post from posts table
$sql = "SELECT * FROM categories";
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
            <h1 class="page-header">Product Categories</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    All the Product Categories 
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Updated</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $sql = "SELECT * FROM categories LIMIT $start, $perpage";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $res = $result->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($res as $cat) {
                                 ?>
                                <tr>
                                    <td><?php echo $cat['id']; ?></td>
                                    <td><?php echo $cat['title']; ?></td>
                                    <td><?php echo $cat['slug']; ?></td>
                                    <td><?php echo $cat['updated']; ?></td>
                                    <td><a href="edit-category.php?id=<?php echo $cat['id']; ?>">Edit</a> | <a href="delete-category.php?id=<?php echo $cat['id']; ?>">Delete</a></td>
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
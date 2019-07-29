<?php 
require_once('../includes/connect.php');
include('includes/check-login.php');
include('includes/header.php');
include('includes/navigation.php'); 
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Products</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    All the Products 
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Categories</th>
                                    <th>Type</th>
                                    <th>Stock</th>
                                    <th>Media</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $sql = "SELECT * FROM products";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    $res = $result->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($res as $product) {
                                        // fetch categories
                                        $catsql = "SELECT categories.title FROM categories JOIN product_categories ON product_categories.cid=categories.id WHERE product_categories.pid=?";
                                        $catresult = $db->prepare($catsql);
                                        $catresult->execute(array($product['id']));
                                        $categories = $catresult->fetchAll(PDO::FETCH_ASSOC);
                                        if($product['type'] == 'digital'){
                                            $mediasql = "SELECT * FROM product_digital WHERE pid=?";
                                            $mediaresult = $db->prepare($mediasql);
                                            $mediaresult->execute(array($product['id']));
                                            $mediacount = $mediaresult->rowCount();
                                            if($mediacount == 1){
                                                $media = "Yes";
                                            }else{
                                                $media = "No";
                                            }
                                        }else{
                                            $media = "-";
                                        }
                                 ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo $product['title']; ?></td>
                                    <td><?php echo $product['price']; ?></td>
                                    <td><?php foreach ($categories as $category) { echo $category['title'].","; } ?></td>
                                    <td><?php echo $product['type']; ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td><?php echo $media; ?></td>
                                    <td><?php echo $product['created']; ?></td>
                                    <td><?php echo $product['status']; ?></td>
                                    <td><a href="edit-product.php?id=<?php echo $product['id']; ?>">Edit</a> | <a href="delete-product.php?id=<?php echo $product['id']; ?>">Delete</a>
                                    <?php 
                                    if($product['type'] == 'physical'){
                                        echo "| <a href='add-stock.php?id={$product['id']}'>Add Stock</a>";
                                    }else{
                                        echo "| <a href='manage-digital-product.php?id={$product['id']}'>Manage Digital Media</a>";
                                    }
                                     ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
            </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<?php include('includes/footer.php'); ?>
<?php 
require_once('../includes/connect.php');
include('includes/check-login.php'); 
if(isset($_POST) & !empty($_POST)){
    // PHP Form Validations
    if(empty($_POST['title'])){ $errors[] = "Title field is Required";}
    if(empty($_POST['description'])){ $errors[] = "Description field is Required";}
    if(empty($_POST['price'])){ $errors[] = "Price field is Required";}
    if(empty($_POST['slug'])){ $slug = trim($_POST['title']); }else{ $slug = trim($_POST['slug']); }
    // check slug in unique with db Query
    $search = array(' ', ',', '.', '_');
    $slug = strtolower(str_replace($search, '-', $slug));
    $sql = "SELECT * FROM products WHERE slug=?";
    $result = $db->prepare($sql);
    $result->execute(array($slug));
    $count = $result->rowCount();
    if($count == 1){
        $errors[] = 'Slug already exists in database';
    }

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
        // Insert into products table
        $sql = "INSERT INTO products (title, description, type, status, price, slug) VALUES (:title, :description, :type, :status, :price, :slug)";
        $result = $db->prepare($sql);
        $values = array(':title'        => $_POST['title'],
                        ':description'  => $_POST['description'],
                        ':type'         => $_POST['type'],
                        ':status'       => $_POST['status'],
                        ':price'        => $_POST['price'],
                        ':slug'         => $slug
                        );
        $res = $result->execute($values) or die(print_r($result->errorInfo(), true));
        if($res){
            header('location: view-products.php');
        }else{
            $errors[] = "Failed to Add Product";
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
            <h1 class="page-header">Add New Product</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Create a New Product Here...
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
                                    <label>Product Title</label>
                                    <input name="title" class="form-control" placeholder="Enter Product Title" value="<?php if(isset($_POST['title'])){ echo $_POST['title']; } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Product Description</label>
                                    <textarea name="description" class="form-control" rows="3"><?php if(isset($_POST['description'])){ echo $_POST['description']; } ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Product Image</label>
                                    <input type="file">
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                        <?php 
                                            $sql = "SELECT * FROM categories";
                                            $result = $db->prepare($sql);
                                            $result->execute();
                                            $res = $result->fetchAll(PDO::FETCH_ASSOC);
                                         ?>                
                                        <label>Product Categories</label>
                                        <select multiple="" name="categories[]" class="form-control">
                                            <?php foreach ($res as $cat) { 
                                                if(in_array($cat['id'], $_POST['categories'])){ $selected = "selected"; }else{$selected = "";}
                                                ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo $selected; ?>><?php echo $cat['title']; ?></option>
                                            <?php } ?>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-offset-1">
                                        <div class="form-group">
                                            <label>Product Type</label>
                                            <div class="radio">
                                            <label>
                                                <input type="radio" name="type" id="optionsRadios1" value="digital" <?php if(isset($_POST['type'])){ if($_POST['type'] == 'digital'){ echo "checked"; } } ?>>Digital
                                            </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="type" id="optionsRadios3" value="physical" <?php if(isset($_POST['type'])){ if($_POST['type'] == 'physical'){ echo "checked"; } } ?>>Physical
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-offset-1">
                                        <div class="form-group">
                                            <label>Product Status</label>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="status" id="optionsRadios1" value="draft" <?php if(isset($_POST['status'])){ if($_POST['status'] == 'draft'){ echo "checked"; } } ?>>Draft
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="status" id="optionsRadios3" value="published" <?php if(isset($_POST['status'])){ if($_POST['status'] == 'published'){ echo "checked"; } } ?>>Published
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Product Price</label>
                                    <input type="number" name="price" class="form-control" placeholder="Enter Product Price Here" value="<?php if(isset($_POST['price'])){ echo $_POST['price']; } ?>">
                                </div>
                                <div class="form-group">
                                    <label>Product Slug</label>
                                    <input name="slug" class="form-control" placeholder="Enter Product Slug Here" value="<?php if(isset($_POST['slug'])){ echo $_POST['slug']; } ?>">
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
<?php include('includes/header.php'); ?>
<?php include('includes/navigation.php'); ?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Setttings</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Shop Settings Here...
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <form role="form">
                                <div class="form-group">
                                    <label>Shop Title</label>
                                    <input class="form-control" placeholder="Enter Shop Title">
                                </div>
                                <div class="form-group">
                                    <label>Shop Tagline</label>
                                    <input type="email" class="form-control" placeholder="Enter Shop Tagline">
                                </div>
                                <div class="form-group">
                                    <label>Shop Email Address</label>
                                    <input class="form-control" placeholder="Enter Shop Email Address">
                                </div>
                                <div class="form-group">
                                    <label>Guest Checkout</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline1" value="option1" checked="">Enabled 
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline2" value="option2">Disabled
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>Products Per Page</label>
                                    <input class="form-control" placeholder="Enter Product Per Page">
                                </div>
                                <div class="form-group">
                                    <label>Reviews</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline1" value="option1" checked="">Enable 
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline2" value="option2">Disable
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>Clean URL's</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline1" value="option1" checked="">Enable 
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline2" value="option2">Disable
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-danger">Reset </button>
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
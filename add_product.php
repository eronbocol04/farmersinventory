<?php
$page_title = 'Add Product';
require_once('includes/load.php');

// Check the user's permission level to view this page
page_require_level(2);

// Fetch all categories and all available photos from the database
$all_categories = find_all('categories');
$all_photo = find_all('media');

$msg = '';

// Check if the form is submitted
if (isset($_POST['add_product'])) {
    // Validate required fields
    $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price', 'selling-price');
    validate_fields($req_fields);

    if (empty($errors)) {
        // Sanitize the user inputs
        $p_name = remove_junk($db->escape($_POST['product-title']));
        $p_cat = remove_junk($db->escape($_POST['product-categorie']));
        $p_qty = remove_junk($db->escape($_POST['product-quantity']));
        $p_buy = remove_junk($db->escape($_POST['buying-price']));
        $p_sale = remove_junk($db->escape($_POST['selling-price']));

        // Handle image upload
        if (isset($_FILES['product-photo']) && $_FILES['product-photo']['error'] === UPLOAD_ERR_OK) {
            $target_directory = "uploads/products/";
            $file_name = $_FILES['product-photo']['name'];
            $target_file = $target_directory . basename($file_name);

            if (move_uploaded_file($_FILES['product-photo']['tmp_name'], $target_file)) {
                // Image uploaded successfully, you can save $file_name in the database
                $media_id = $db->escape($file_name);
            } else {
                // Image upload failed
                $media_id = '';
            }
        } else {
            // No image uploaded
            $media_id = '';
        }

        // Insert product details into the database
        $date = make_date();
        $query = "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, media_id, date)";
        $query .= " VALUES ('{$p_name}', '{$p_qty}', '{$p_buy}', '{$p_sale}', '{$p_cat}', '{$media_id}', '{$date}')";
        $query .= " ON DUPLICATE KEY UPDATE name='{$p_name}'";

        if ($db->query($query)) {
            $session->msg('s', "Product added");
            redirect('add_product.php', false);
        } else {
            $session->msg('d', 'Failed to add the product.');
            redirect('product.php', false);
        }

    } else {
        $session->msg("d", $errors);
        redirect('add_product.php', false);
    }
}

// Get the list of products from the database (You need to implement the find_all function)
$products = find_all('products');

// Include the header file
include_once('layouts/header.php');
?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Add New Product</span>
                </strong>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <form method="post" action="add_product.php" class="clearfix" enctype="multipart/form-data">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-th-large"></i>
                                </span>
                                <input type="text" class="form-control" name="product-title"
                                    placeholder="Product Title">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-control" name="product-categorie">
                                        <option value="">Select Product Category</option>
                                        <?php foreach ($all_categories as $cat): ?>
                                            <option value="<?php echo (int) $cat['id'] ?>">
                                                <?php echo $cat['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label class="input-group-btn">
                                            <span class="btn btn-primary">
                                                Select Product Photo&hellip;
                                                <input type="file" style="display: none;" name="product-photo"
                                                    accept="image/*" onchange="previewImage(event)">
                                            </span>
                                        </label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                    <div class="image-preview">
                                        <img id="preview" src="" alt="Product Preview" class="img-responsive">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-shopping-cart"></i>
                                        </span>
                                        <input type="number" class="form-control" name="product-quantity"
                                            placeholder="Product Quantity">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </span>
                                        <input type="number" class="form-control" name="buying-price"
                                            placeholder="Buying Price">
                                        <span class="input-group-addon">.00</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </span>
                                        <input type="number" class="form-control" name="selling-price"
                                            placeholder="Selling Price">
                                        <span class="input-group-addon">.00</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-danger">Add product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<script>

    // Function to display the selected image preview
    function previewImage(event) {
        var input = event.target;
        var reader = new FileReader();

        reader.onload = function () {
            var output = document.getElementById('preview');
            output.src = reader.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
</script>
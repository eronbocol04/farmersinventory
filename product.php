<?php
$page_title = 'All Product';
require_once('includes/load.php');
// Checking what level user has permission to view this page
page_require_level(2);
$products = join_product_table();
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="pull-left"> <!-- Move the search bar to the left -->
          <form class="form-inline">
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Search" name="search" id="searchInput">
            </div>
            <button type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
          </form>
        </div>
        <div class="pull-right">
          <a href="add_product.php" class="btn btn-primary">Add New</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered" id="numbersTable">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th> Photo</th>
              <th> Product Title </th>
              <th class="text-center" style="width: 10%;"> Categories </th>
              <th class="text-center" style="width: 10%;"> In-Stock </th>
              <th class="text-center" style="width: 10%;"> Buying Price </th>
              <th class="text-center" style="width: 10%;"> Selling Price </th>
              <th class="text-center" style="width: 10%;"> Product Added </th>
              <th class="text-center" style="width: 100px;"> Actions </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product) : ?>
              <tr>
                <td class="text-center"><?php echo $product['id']; ?></td>
                <td>
                  <?php if (!empty($product['image']) && file_exists("uploads/products/" . $product['image'])) : ?>
                    <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['image']; ?>" alt="">
                  <?php else : ?>
                    <img class="img-avatar img-circle" src="" alt="">
                  <?php endif; ?>
                </td>
                <td> <?php echo remove_junk($product['name']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['categorie']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['quantity']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['buy_price']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['sale_price']); ?></td>
                <td class="text-center"> <?php echo read_date($product['date']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_product.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-info btn-xs" title="Edit" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_product.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-danger btn-xs" title="Delete" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>

<script>
  // Function to handle the search functionality
  function searchTable() {
    var input, filter, table, tr, tdId, tdName, i, txtValueId, txtValueName;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("numbersTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
      tdId = tr[i].getElementsByTagName("td")[0];
      tdName = tr[i].getElementsByTagName("td")[2]; // Update this with the correct column index for the product title.

      if (tdId && tdName) {
        txtValueId = tdId.textContent || tdId.innerText;
        txtValueName = tdName.textContent || tdName.innerText;

        if (txtValueId.toUpperCase().indexOf(filter) > -1 || txtValueName.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";

          // Highlight matching letters
          tdId.innerHTML = txtValueId.replace(new RegExp(filter, 'gi'), function (match) {
            return '<span class="highlight">' + match + '</span>';
          });

          tdName.innerHTML = txtValueName.replace(new RegExp(filter, 'gi'), function (match) {
            return '<span class="highlight">' + match + '</span>';
          });
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  // Add event listener for the input event on the search input field
  document.getElementById("searchInput").addEventListener("input", searchTable);
</script>

<?php
session_start();
include '../../partials/dbConnect.php';
$db = new Database();

$customer_id = $_SESSION['user_id'];
$reviews = $db->getProductRatingByCustomerId($customer_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My review</title>
  <link rel="stylesheet" href="myReview.css?v=<?php echo time(); ?>">

  <link rel="stylesheet" href="../HeaderPage/head.css">
  <link rel="stylesheet" href="../FooterPage/footer.css">
  <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <div><?php include('../HeaderPage/head.php'); ?></div>
  <div class="wrapper">
    <div class="sidebar">
      <ul>
        <li><a href="customerProfile.php"><i class="fas fa-user"></i>My Profile</a></li>
        <li><a href="myOrder.php"><i class="fas fa-cart-shopping"></i>My Orders</a></li>
        <li><a href="myWishlist.php"><i class="fas fa-heart"></i>My Whislists</a></li>
        <li><a href="myReview.php"><i class="fas fa-money-bill"></i>My Reviews</a></li>
        <li><a href="myCart.php"><i class="fas fa-cart-shopping"></i>My Cart</a></li>
      </ul>
    </div>
    <div class="main_content">
      <div class="header">My Review</div>
      <div class="table-container">
        <table class="review-table">
          <thead>
            <tr>
              <th scope="col">Product Image</th>
              <th scope="col">Product Name</th>
              <th scope="col">Rating</th>
              <th scope="col">Review</th>
              <th scope="col">Reviewed Date</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reviews as $review): ?>
                <?php
                    $productId = $review["PRODUCT_ID"];
                    $imageBase64 = $db->getProductImage($productId);
                ?>
                <tr>
                    <td class="img-container">
                        <?php if ($imageBase64): ?>
                            <img src="data:image/jpeg;base64,<?php echo $imageBase64; ?>" alt="<?php echo htmlspecialchars($review['PRODUCT_NAME']); ?>" style="width: 100%; height: 130px;">
                        <?php else: ?>
                            <img src="../Image/path_to_placeholder_image.jpg" alt="<?php echo htmlspecialchars($review['PRODUCT_NAME']); ?> Image" style="width: 100%; height: auto;">
                        <?php endif; ?>
                    </td>
                    <td class="td-product-name"><?php echo htmlspecialchars($review['PRODUCT_NAME']); ?></td>
                    <td class="td-rating">
                      <?php 
                          $rating = $review['RATING'];
                          for ($i = 1; $i <= 5; $i++) {
                              if ($i <= $rating) {
                                  echo '<i class="fas fa-star text-warning" style="color: #ffc107;"></i>';
                              } else {
                                  echo '<i class="far fa-star" style="color: #ffc107;"></i>';
                              }
                          }
                      ?>
                    </td>
                    <td>
                        <textarea name="review" rows="6" cols="60" class="form-control"><?php echo htmlspecialchars($review['COMMENTS']); ?></textarea>
                    </td>
                    <td class="td-review-date"><?php echo htmlspecialchars($review['REVIEWED_DATE']); ?></td>
                    <td class="td-actions">
                        <a href="review_action.php?delete_reviewId=<?php echo $review['REVIEW_ID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                        <a href="#" class="btn btn-warning btn-sm edit-btn" data-review-id="<?php echo $review['REVIEW_ID']; ?>">Edit</a>
                    </td>
                </tr>
                <tr class="edit-form" id="edit-form-<?php echo $review['REVIEW_ID']; ?>" style="display: none;">
                  <td colspan="6">
                      <form action="review_action.php" method="post" class="edit-review-form">
                          <input type="hidden" name="edit_reviewId" value="<?php echo $review['REVIEW_ID']; ?>">
                          <div class="form-group">
                              <label for="editedComment">Edit Your Review:</label>
                              <textarea name="editedComment" rows="6" cols="60" class="form-control" id="editedComment"><?php echo htmlspecialchars($review['COMMENTS']); ?></textarea>
                          </div>
                          <button type="submit" class="btn btn-primary btn-sm">Save</button>
                          <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn">Cancel</button>
                      </form>
                  </td>
              </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php include('../FooterPage/footer.php'); ?>
  <script src="../HeaderPage/head.js"></script>

<script>
  document.querySelectorAll('.edit-btn').forEach(button => {
      button.addEventListener('click', () => {
          const reviewId = button.getAttribute('data-review-id');
          const editForm = document.getElementById(`edit-form-${reviewId}`);
          if (editForm.style.display === 'none') {
              editForm.style.display = 'table-row';
          } else {
              editForm.style.display = 'none';
          }
      });
  });

  document.querySelectorAll('.cancel-edit-btn').forEach(button => {
      button.addEventListener('click', () => {
          const reviewId = button.closest('.edit-form').getAttribute('id').replace('edit-form-', '');
          const editForm = document.getElementById(`edit-form-${reviewId}`);
          editForm.style.display = 'none';
      });
  });
</script>


</body>
</html>

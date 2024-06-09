<?php

session_start();
include '../../partials/dbConnect.php';
$db = new Database();

$customerId = $_SESSION['user_id'];

if (isset($_GET['product_id'])) {
    $productId = intval($_GET['product_id']);
    $result = $db->updateOrInsertCartItem($customerId, $productId, 1, '');
    $db->removeFromWishlist($customerId, $productId);
    header("Location: myWishList.php?customer_id=$customerId");
    exit;
}

if (isset($_GET['remove_product_id'])) {
    $productIdToRemove = intval($_GET['remove_product_id']);
    $db->removeFromWishlist($customerId, $productIdToRemove);
    header("Location: myWishlist.php");
    exit;
}

$wishlistItems = $db->getProductFromWishlist($customerId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wishlist</title>
    <link rel="stylesheet" href="myWishlist.css">
    <link rel="stylesheet" href="../HeaderPage/head.css">
    <link rel="stylesheet" href="../FooterPage/footer.css">
    <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div><?php include('../HeaderPage/head.php'); ?></div>
    <div class="wrapper">
    <div class="sidebar">
            <ul>
                <li><a href="customerProfile.php"><i class="fas fa-user"></i>My Profile</a></li>
                <li><a href="myOrder.php"><i class="fas fa-cart-shopping"></i>My Orders</a></li>
                <li><a href="myWishlist.php"><i class="fas fa-heart"></i>My Whislists</a></li>
                <li><a href="myReview.php"><i class="fas fa-money-bill"></i></i>My Reviews</a></li>
                <li><a href="myCart.php"><i class="fas fa-cart-shopping"></i>My Cart</a></li>
            </ul>
        </div>
        <div class="main_content">
            <div class="hr">My Wishlist</div>
            <div class="wish_content">
                <table class="table wishlist-table">
                    <thead>
                        <tr>
                            <th scope="col">Product Image</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Stock Status</th>
                            <th scope="col">Category</th>
                            <th scope="col">Shop Name</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($wishlistItems as $item):
                    // Get the base64 image data for the product
                    $product_id = $item['PRODUCT_ID'];
                    $imageBase64 = $db->getProductImage($item['PRODUCT_ID']);
                    ?>
                        <tr>
                            <td class="img-container">
                                <?php
                            if ($imageBase64) {
                                echo '<img src="data:image/jpeg;base64,' . $imageBase64 . '" alt="' . htmlspecialchars($item['PRODUCT_NAME']) . '" style="width: 100%; height: 130px;">';
                            } else {
                                echo '<img src="../Image/path_to_placeholder_image.jpg" alt="' . htmlspecialchars($item['PRODUCT_NAME']) . ' Image" style="width: 100%; height: auto;">';
                            }
                            ?>
                            </td>
                            <td class="td-product-name"><?php echo htmlspecialchars($item['PRODUCT_NAME']); ?></td>
                            <td class="td-price"><?php echo htmlspecialchars($item['PRICE']); ?></td>
                            <td class="td-stock"><?php echo htmlspecialchars($item['STOCK']); ?></td>
                            <td class="td-category"><?php echo htmlspecialchars($item['CATEGORY_NAME']); ?></td>
                            <td class="td-shop-name"><?php echo htmlspecialchars($item['SHOP_NAME']); ?></td>
                            <td class="td-actions">
                                <a href="myWishlist.php?remove_product_id=<?php echo $item['PRODUCT_ID']; ?>"
                                    class="btn btn-danger btn-sm">Remove</a>
                                <a href="myWishList.php?product_id=<?php echo $product_id; ?>"
                                    class="btn btn-primary btn-sm" style="font-family: 'Roboto', sans-serif;
                            font-size: 1.8rem;">Add to Cart</a>
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
</body>

</html>
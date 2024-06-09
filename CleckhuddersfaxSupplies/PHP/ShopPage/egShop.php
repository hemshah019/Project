<?php
session_start();
require_once '../../partials/dbConnect.php';
require_once '../alertService.php';

$db = new Database();
$conn = $db->getConnection();
$shopImageBase64 = null;
$shopID = null;


if (isset($_GET['shopID']) && is_numeric($_GET['shopID'])) {
  $shopID   = (int)$_GET['shopID'];
    // echo "Shop ID: $shopID<br>"; 
} else {
    $_SESSION['error'] = "Shop ID not specified or invalid";
    echo $_SESSION['error'];
    exit();
}

$queryShop = "SELECT * FROM SHOP WHERE SHOP_ID = :shopID";
$statement_Shop = oci_parse($conn, $queryShop);
if (!$statement_Shop) {
    $e = oci_error($conn);
    echo "Error preparing queryShop: " . htmlspecialchars($e['message']);
    exit();
}
oci_bind_by_name($statement_Shop, ':shopID', $shopID);
if (!oci_execute($statement_Shop)) {
    $e = oci_error($statement_Shop);
    echo "Error executing queryShop: " . htmlspecialchars($e['message']);
    exit();
}
$fetchShop = oci_fetch_assoc($statement_Shop);
if (!$fetchShop) {
    $_SESSION['error'] = "Shop not found";
    echo $_SESSION['error'];
    exit();
}
$products = $db->getProductsByShopId($shopID);
oci_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Page</title>
    <link rel="stylesheet" href="egShop.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="../HeaderPage/head.css">
    <?php   AlertService::includeCSS(); ?>
</head>
<body>
    <?php
        AlertService::displayAlerts();
    ?>
    <div><?php include('../HeaderPage/head.php'); ?></div>
    <div class="main-container">
        <div class="shop-container">
            <div class="shop-info">
            <?php
            $shopImageBase64 = $db->getShopImage($fetchShop['SHOP_ID']);
            ?>
            <?php if ($shopImageBase64): ?>
                <img class="shop-image" src="data:image/jpeg;base64,<?php echo $shopImageBase64; ?>" alt="<?php echo htmlspecialchars($fetchShop['SHOP_NAME']); ?>">
            <?php else: ?>
                <img class="shop-image" src="../Image/path_to_placeholder_image.jpg" alt="<?php echo htmlspecialchars($fetchShop['SHOP_NAME']); ?> Image">
            <?php endif; ?>
                <h1 class="shop-name"><?php echo htmlspecialchars($fetchShop['SHOP_NAME']); ?></h1>
                <p class="shop-description"><?php echo htmlspecialchars($fetchShop['DESCRIPTION']); ?></p>
            </div>
        </div>
        <div class="product-container">
            <div class="product-list">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>  
                        <?php
                           $rating = $product["RATING"];  
                        ?>
                        <div class="product-item">
                            <a href="../HomePage/productdtl.php?product_id=<?php echo $product['PRODUCT_ID']; ?>">
                                <?php
                                $imageBase64 = $db->getProductImage($product['PRODUCT_ID']);
                                ?>
                                <?php if (!empty($imageBase64)): ?>
                                    <img src="data:image/jpeg;base64,<?php echo $imageBase64; ?>" alt="Product Image" class="product-image">
                                <?php else: ?>
                                    <img src="../Image/apple.jpeg" alt="No Image" class="product-image">
                                <?php endif; ?>
                            </a>
                            <h2 class="product-name">
                                <a href="../HomePage/productdtl.php?product_id=<?php echo $product['PRODUCT_ID']; ?>">
                                    <?php echo htmlspecialchars($product['PRODUCT_NAME']); ?>
                                </a>
                            </h2>
                            <div class="price">£<?php echo htmlspecialchars($product['PRICE']); ?></div>
                            <div class="product-rating"> 
                                <?php
                                for ($i = 0; $i < 5; $i++) {
                                    if ($i < $rating) {
                                        echo '<i class="fas fa-star text-warning"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="btn-container">
                                <a href="../HomePage/addToCart.php?productid=<?php echo $product['PRODUCT_ID']; ?>" class="btn add-to-cart">Add to Cart</a>
                                <a href="../HomePage/addToWishlist.php?product_id=<?php echo $product['PRODUCT_ID']; ?>" class="btn add-to-wishlist">Add to Wishlist</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products available</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- <br><br><br><br><br><br><br> -->
    <script src="../HeaderPage/head.js"></script>
</body>
</html>

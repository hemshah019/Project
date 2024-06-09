<?php
    require_once('../../partials/dbconnect.php');

    $database = new Database();

    $productID = isset($_GET['product_id']) ? $_GET['product_id'] : null;
    $product = $database->getProductById($productID);
    $shopId = $product['SHOP_ID'];

    $imageBase64 = $database->getProductImage($productID);
    $reviews = $database->getReviewsForAProduct($productID);

    $productQuantity = 1;

    $database->closeConnection();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="productdtl.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../HeaderPage/head.css">
    <link rel="stylesheet" href="../FooterPage/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
    <div>
        <?php include('../HeaderPage/head.php'); ?>
    </div>

    <div class="container my-5">
        <div class="row">
            <?php if ($product && $imageBase64) : ?>
            <div class="col-md-5">
                <div class="main-img">
                    <!-- Resize and display the product image -->
                    <img class="img-fluid" src="data:image/png;base64, <?php echo $imageBase64; ?>" alt="Product"
                        width="100" height="100">
                </div>
                <div class="text-center mt-3">
                    <div class="d-flex justify-content-between">
                        <!-- "Add to Wishlist" button with icon -->
                        <a href="addToWishlist.php?product_id=<?php echo $productID; ?>"
                            class="btn btn-outline-secondary"
                            style="font-family: 'Roboto', sans-serif; font-size: 1.8rem; background-color: #6c757d; border: none; color: #FFF">
                            <i class="fas fa-heart"></i> Add to Wishlist
                        </a>
                        <!-- Add to Cart button -->
                        <?php
                        echo '<a href="addToCart.php?productid=' . $productID . '" class="btn btn-primary" style="font-family: \'Roboto\', sans-serif; font-size: 1.8rem; background-color: #ff8000; border: none; ">
                        <i class="fas fa-shopping-cart"></i> Add to Cart </a>';
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <!-- Product details -->
                <div class="main-description px-2">
                    <div class="product-title text-bold my-3">
                        <?php echo $product['PRODUCT_NAME']; ?>
                        <!-- Product Name -->
                    </div>
                    <div class="price-area my-4">
                        <?php if ($product['PRICE']) : ?>
                        <p class="new-price text-bold mb-1">Price: £<?php echo $product['PRICE']; ?></p>
                        <?php endif; ?>
                        <!-- Display Rating -->
                        <?php
                        $rating = $product['RATING'];
                        for ($i = 0; $i < $rating; $i++) {
                            echo '<span class="fa fa-star"></span>';
                        }
                        ?>
                    </div>
                    <div class="product-details my-4">
                        <!-- Display Product Description -->
                        <p class="description"><?php echo $product['DESCRIPTION']; ?></p>
                    </div>
                    <!-- Additional Product Details -->
                    <div class="product-detail my-4">
                        <p><strong>Category:</strong> <?php echo $product['CATEGORY_NAME']; ?></p>
                        <p><strong>Shop:</strong> <?php echo $product['SHOP_NAME']; ?></p>
                        <p><strong>Stock:</strong> <?php echo $product['STOCK']; ?></p>
                        <p><strong>Discount:</strong> <?php echo $product['DISCOUNT_PERCENTAGE']; ?></p>
                        <p><strong>Allergy Info:</strong> <?php echo $product['ALLERGY_INFO']; ?></p>
                    </div>
                </div>
            </div>
            <?php else : ?>
            <div class="col-md-12">
                <p>Product not found.</p>
            </div>
            <?php endif; ?>
        </div>

        <script>
        // Initialize productQuantity JavaScript variable with the value from PHP
        var productQuantity = <?php echo $productQuantity; ?>;

        // JavaScript function to update quantity
        function updateQuantity(action) {
            if (action === 'increase') {
                productQuantity++;
            } else if (action === 'decrease' && productQuantity > 1) {
                productQuantity--;
            }
            document.getElementById('productQuantity').innerText = productQuantity;
        }
        </script>

        <div class="container">
            <div class="row">
                <div class="review">
                    <input id="btnBox" type="checkbox">
                    <p class="display-5">Product Reviews</p>
                    <ul>
                        <?php foreach ($reviews as $review): ?>
                        <li>
                            <div class="rev" style="width: 1094px;">
                                <div class="name_date">
                                    <p class="pname"><?php echo htmlspecialchars($review['CUSTOMER_NAME']); ?></p>
                                    <p class="rev_date"><?php echo htmlspecialchars($review['REVIEW_DATE']); ?></p>
                                </div>
                                <?php for ($i = 0; $i < $review['RATING']; $i++): ?>
                                <span class="fa fa-star"></span>
                                <?php endfor; ?>
                                <?php for ($i = $review['RATING']; $i < 5; $i++): ?>
                                <span class="fa fa-star-o"></span>
                                <?php endfor; ?>
                                <p><?php echo htmlspecialchars($review['REVIEW_TEXT']); ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <label class="btn_a" for="btnBox"><span class="btn1">See More</span><span class="btn2">See
                            Less</span></label>
                </div>
            </div>
        </div>

        <div class="container similar-products my-4">
            <hr>
            <p class="display-5">Similar Products</p>
            <div class="row">
                <?php
            // Fetch similar products
            $similarProducts = $database->getProductsByShopId($shopId);

            // Loop through similar products and display them
            foreach ($similarProducts as $product) {
                // Fetch image base64
                $imageBase64 = $database->getProductImage($product['PRODUCT_ID']);
                $productId = $product['PRODUCT_ID'];
                $price = '£' . number_format($product['PRICE'], 2);
                $stock = $product['STOCK'] > 0 ? '<span class="text-success">In Stock</span>' : '<span class="text-danger">Out of Stock</span>';
                $avgRating = $product['RATING'];
                $noRating = 5 - $avgRating;
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-border d-flex">
                        <?php if ($imageBase64) : ?>
                        <a href="../HomePage/productdtl.php?product_id=<?php echo $productId; ?>">
                            <img src="data:image/jpeg;base64,<?php echo $imageBase64; ?>" class="card-img-top"
                                alt="product image" style="width: 100%; height: 240px;">
                        </a>
                        <?php else : ?>
                        <a href="../HomePage/productdtl.php?product_id=<?php echo $productId; ?>">
                            <img src="path_to_placeholder_image.jpg" class="card-img-top"
                                alt="<?php echo htmlspecialchars($product['PRODUCT_NAME']); ?> Image"
                                style="width: 100%; height: auto;">
                        </a>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="card-upper-body text-left">
                                <h5 class="card-title"><a
                                        href="../HomePage/productdtl.php?product_id=<?php echo $productId; ?>"
                                        style="color: #333; font-size: 1.75rem;"><?php echo htmlspecialchars($product['PRODUCT_NAME']); ?></a>
                                </h5>
                                <h6 class="card-text text-muted"
                                    style="font-family: 'Roboto', sans-serif; font-size: 1.5rem;">
                                    <?php echo substr(htmlspecialchars($product['DESCRIPTION']), 0, 50); ?>...</h6>
                                <h6 class="card-title" style="font-size: 1.5rem;"><?php echo $price; ?></h6>
                                <?php echo $stock; ?>
                            </div>
                            <div class="star d-flex">
                                <?php echo str_repeat('<i class="bi bi-star-fill me-1 text-warning"></i>', $avgRating); ?>
                                <?php echo str_repeat('<i class="bi bi-star me-1"></i>', $noRating); ?>
                            </div>
                            <div class="btn-group mt-2" role="group" aria-label="Product Actions">
                                <a href="addToCart.php?productid=<?php echo $productId; ?>" class="btn btn-primary"
                                    style="font-family: 'Roboto', sans-serif; font-size: 1.5rem; background-color: #ff8000; border: none;">Add to Cart</a>
                                <a href="addToWishlist.php?product_id=<?php echo $productId; ?>"
                                    class="btn btn-outline-secondary ml-2"
                                    style="font-family: 'Roboto', sans-serif; font-size: 1.5rem; background-color: #6c757d; border: none; color: #FFF">Add to Wishlist</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            </div>
        </div>


        <h1 class="custom-heading">
            Show <a href="../ShopPage/egShop.php?shopID=<?php echo $shopId; ?>" class="more-link">More</a> Products from
            This Shop
        </h1>


    </div>

    <?php require('../FooterPage/footer.php'); ?>
</body>

</html>
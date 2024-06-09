<?php 
require_once '../alertService.php' ;
global $shops;
$shops = null;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="homepage.css?<?php echo time();?>">
    <link rel="stylesheet" href="../HeaderPage/head.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="../FooterPage/footer.css?<?php echo time(); ?>">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Cleckhuddersfax Supplies</title>
    <?php AlertService::includeCSS(); ?>


</head>

<body>
    <?php
    AlertService::displayAlerts();
    ?>
    <div>
        <?php
            include('../HeaderPage/head.php');
        ?>
    </div>

    <section class="home" id="home">
        <div class="content">
            <h3>fresh and <span>organic</span> products for your</h3>
            <p>Crafting Moments, One Ingredient at a Time: Welcome to Our Cleckhuddersfax Supplies!</p>
            <a href="../ProductPage/product.php" class="btns">Shop Now</a>
        </div>
    </section>

    <section class="features" id="features">
        <h1 class="heading">Our <span>Features</span></h1>

        <div class="box-container">
            <div class="box">
                <img src="../Image/fresh.jpeg" alt="">
                <h3>Fresh Market Outlets</h3>
                <p>Where quality meets culinary inspiration, offering a curated selection of premium groceries and
                    gourmet
                    delights."</p>
            </div>

            <div class="box">
                <img src="../Image/Delivery.jpeg" alt="">
                <h3>Collection Slots</h3>
                <p>Enjoy the convenience of collection day on (Wednesday, Thursday & Friday) with Cleckhuddersfax Supplies, making shopping effortless..</p>
            </div>

            <div class="box">
                <img src="../Image/easy payment.jpeg" alt="">
                <h3>easy payments</h3>
                <p>Join us at Cleckhuddersfax Supplies and experience the ease of stress-free PayPal payments, tailored to your convenience.</p>
            </div>
        </div>
    </section>


    <!-- Product container starts here -->
    <div class="container my-4" id="ques">
        <h1 class="heading">Our <span>Products</span></h1>
        <div class="row my-3">
            <!-- fetch all the products and use a loop to iterate through products -->
            <?php
            require_once '../../partials/dbConnect.php';
            $db = new Database();
            $shops = $db->getAllShops();

            $products = $db->getProducts();

            foreach ($products as $product) {
                $id = $product["PRODUCT_ID"];
                $name = $product["PRODUCT_NAME"];
                $desc = $product["DESCRIPTION"];
                $rating = $product["RATING"]; 
                $price = "£".$product['PRICE'];
                $stock = $product['STOCK'] > 1 ? "<p class=\"text-success\">In Stock<p>" : "<p class=\"text-warning\">No Stock<p>";
                $imageBase64 = $db->getProductImage($id);

                echo '<div class="col-md-4 my-4 product-card">
                        <div class="card border rounded shadow-sm" style="width: 100%; border-color: #ddd; height: 420px;">'; // Set a fixed height for the card container

                echo '<div style="height: 200px; overflow: hidden;">'; // Set a fixed height for the image container and hide overflow
                if ($imageBase64) {
                    echo '<a href="productdtl.php?product_id=' . $id . '">';
                    echo '<img src="data:image/jpeg;base64,' . $imageBase64 . '" alt="Customer Image" style="width: 100%; height: auto;">';
                    echo '</a>';
                } else {
                    echo '<a href="productdtl.php?product_id=' . $id . '">';
                    echo '<img src="path_to_placeholder_image.jpg" alt="' . $name . ' Image" style="width: 100%; height: auto;">';
                    echo '</a>';
                }
                echo '</div>'; // Close image container

                echo '<div class="card-body" style="padding: 1.5rem;">';

                echo '<h5 class="card-title" style="font-family: \'Roboto\', sans-serif; font-size: 2rem;"><a href="productdtl.php?product_id=' . $id . '" style="color: #333;">' . $name . '</a></h5>
                    <p class="card-text text-muted" style="font-family: \'Roboto\', sans-serif; font-size: 1.5rem;">' . substr($desc, 0, 50) . '...</p>
                    <div class="ratings">';
                echo '<h6 class="card-title" style="font-size: 1.5rem;">' . $price . '</h6>';
                echo $stock ;
                // Display star rating
                for ($i = 0; $i < 5; $i++) {
                    if ($i < $rating) {
                        echo '<i class="fas fa-star text-warning"></i>';
                    } else {
                        echo '<i class="far fa-star"></i>';
                    }
                }
                echo '</div>
                        <div class="btn-group mt-0" role="group" aria-label="Product Actions">'; 
                // Increased font size for buttons
                echo '<a href="addToCart.php?productid=' . $id . '" class="btn btn-primary" style="font-family: \'Roboto\', sans-serif; font-size: 1.8rem; background-color: #ff8000; border: none;">Add to Cart</a>';
                echo '<a href="addToWishlist.php?product_id=' . $id . '" class="btn btn-outline-secondary ml-2" style="font-family: \'Roboto\', sans-serif; font-size: 1.8rem; background-color: #6c757d; border: none; color: #FFF">Add to Wishlist</a>';
                echo '</div>
                </div>
                </div>
                </div>';
            }
            ?>
        </div>
        <h1 class="heading">Show <a href="../ProductPage/product.php" class="more-link">More</a> Products</h1>
    </div>


    <!-- Include Bootstrap JS and dependencies here -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
    </script>


    <!-- Showing our shops -->
    <section class="categores" id="categores">
        <h1 class="heading">Our <span>Shops</span></h1>

        <div class="box-container">
            <?php if (!empty($shops)): ?>
            <?php foreach ($shops as $shop): ?>


            <div class="box">
                <?php
                        $shopImageBase64 = $db->getShopImage($shop['SHOP_ID']);
                        $shopPageUrl = "../ShopPage/egShop.php?shopID=" . urlencode($shop['SHOP_ID']);
                        ?>
                <?php if ($shopImageBase64): ?>
                <a href="<?php echo $shopPageUrl; ?>">
                    <img src="data:image/jpeg;base64,<?php echo $shopImageBase64; ?>"
                        alt="<?php echo htmlspecialchars($shop['SHOP_NAME']); ?>">
                </a>
                <?php else: ?>
                <a href="<?php echo $shopPageUrl; ?>">
                    <img src="../Image/path_to_placeholder_image.jpg"
                        alt="<?php echo htmlspecialchars($shop['SHOP_NAME']); ?> Image">
                </a>
                <?php endif; ?>

                <h3>
                    <a href="<?php echo $shopPageUrl; ?>">
                        <?php echo htmlspecialchars($shop['SHOP_NAME']); ?>
                    </a>
                </h3>
                <p><?php echo htmlspecialchars($shop['DESCRIPTION']); ?></p>
                <a href="<?php echo $shopPageUrl; ?>" class="btns">Shop now</a>
            </div>

            <?php endforeach; ?>
            <?php else: ?>
            <p>No shops found.</p>
            <?php endif; ?>
        </div>

    </section>
    <br><br><br>

    <?php include('../FooterPage/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="homepage.js"></script>
    <script src="../HeaderPage/head.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
    </script>
</body>

</html>
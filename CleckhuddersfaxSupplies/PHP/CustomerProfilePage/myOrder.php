<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Orders</title>
    <link rel="stylesheet" href="myOrder.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../HeaderPage/head.css">
    <link rel="stylesheet" href="../FooterPage/footer.css">
    <script defer src="order.js"></script>
    <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
    <div><?php include('../HeaderPage/head.php'); ?></div>
    <div class="modal" id="reviewModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-title">Add Review For: <span id="productName"></span></h2>
            <div class="review-form">
                <form id="reviewForm">
                    <div class="rating-section">
                        <label for="rating">Rating:</label>
                        <div class="rating-stars" id="ratingStars">
                            <!-- Five stars, initially all empty -->
                            <i class="far fa-star star" data-rating="1"></i>
                            <i class="far fa-star star" data-rating="2"></i>
                            <i class="far fa-star star" data-rating="3"></i>
                            <i class="far fa-star star" data-rating="4"></i>
                            <i class="far fa-star star" data-rating="5"></i>
                        </div>
                        <!-- Hidden input field to store the selected rating -->
                        <input type="hidden" id="rating" name="rating" value="0">
                    </div>
                    <div class="comment-section">
                        <label for="comments">Comments:</label>
                        <textarea id="comments" name="comments" rows="4" placeholder="Enter your comments here..."
                            required></textarea>
                    </div>
                    <div class="submit-section">
                        <input type="submit" value="Submit Review">
                    </div>
                </form>
            </div>
        </div>
    </div>


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
            <div class="hr">My Orders</div>
            <?php
        // Include database connection file
        require_once('../../partials/dbconnect.php');
        $customerId = $_SESSION['user_id'];
        $db = new Database();

        // Fetch order details from the database
        $orders = $db->getOrderDetailsFromDatabase($customerId);

        // Loop through orders
        foreach ($orders as $order) {
            // Initialize total price for each order
            $totalPrice = 0;

            echo "<div class='order-details'>";
            echo "<h2>Order ID: {$order['order_id']}</h2>";
            echo "<p>Status: {$order['status']}</p>";
            echo "<p>Date: {$order['order_date']}</p>";
            echo "<div class='order-container'>";
            echo "<table class='table order-table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th scope='col'>Product Image</th>";
            echo "<th scope='col'>Product Name</th>";
            echo "<th scope='col'>Price (£)</th>";
            echo "<th scope='col'>Quantity</th>";
            echo "<th scope='col'>Sub Total (£)</th>";
            echo "<th scope='col'>Actions</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            // Loop through order products
            foreach ($order['order_products'] as $product) {
                $productId = $product['product_id'];
                $imageBase64 = $db->getProductImage($productId);
                $subtotal = ($product['price'] - ($product['price'] * $product['discount'] / 100)) * $product['quantity'];
                $totalPrice += $subtotal;
                echo "<tr>";
                echo "<td class='img-container'>";
                if ($imageBase64) {
                    echo '<img src="data:image/jpeg;base64,' . $imageBase64 . '" alt="' . htmlspecialchars($product['product_name']) . '" style="width: 100%; height: 130px;">';
                } else {
                    echo '<img src="../Image/path_to_placeholder_image.jpg" alt="' . htmlspecialchars($product['product_name']) . ' Image" style="width: 100%; height: auto;">';
                }
                echo "</td>";
                echo "<td class='td-product-name'>{$product['product_name']}</td>";
                echo "<td class='td-price'>£{$product['price']}</td>";
                echo "<td class='th-quantity'>{$product['quantity']}</td>";
                echo "<td class='td-sub-total'>£{$subtotal}</td> "; // Show the calculated subtotal
                echo "<td>";
                echo "<button class='review-btn' data-product-id='{$product['product_id']}' data-product-name='{$product['product_name']}' data-customer-id='{$_SESSION['user_id']}'>Review</button>";
                echo "<button class='buy-again-btn' onclick=\"window.location.href='../HomePage/addToCart.php?productid={$productId}'\">Buy Again</button>";


                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            echo "<div class='total-price'>Total Price: £" . number_format($totalPrice, 2) . "</div>";
            echo "</div>";
            echo "</div>";
        }
        ?>
        </div>
    </div>
    <?php include('../FooterPage/footer.php'); ?>
</body>
</html>
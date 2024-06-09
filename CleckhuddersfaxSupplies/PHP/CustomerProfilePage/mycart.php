<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once '../../partials/dbConnect.php';
    require_once '../alertService.php';


    if(!(isset($_SESSION['isAuthenticated']) && isset($_SESSION['loggedin']) && isset($_SESSION['username']))) {
        AlertService::setWarning('You must first log in to see your cart!');
        header("Location: ../Login_Signup/login.php");
    }

    $customerid = $_SESSION['user_id'];
    $db = new Database();
    $cartid = $db->getCartIdUsingCustomerId($customerid);
    $cart_products = $db->getCartProducts($cartid);

    $subtotal = 0;
    foreach ($cart_products as $product) {
        $subtotal += $product['PRICE'] * $product['QUANTITY'];
    }
    $tax = 0;
    $total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="myCart.css?<?php echo time(); ?>">

    <link rel="stylesheet" href="../HeaderPage/head.css">
    <link rel="stylesheet" href="../FooterPage/footer.css">
    <!-- Include FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <?php AlertService::includeCSS(20); ?>
    <title>My Cart</title>

</head>

<body>
    <?php AlertService::displayAlerts(); ?>
    <div><?php include('../HeaderPage/head.php');?></div>
    <!-- Cart items details -->
    <div class="wrapper">
        <div class="sidebar">
            <ul>
                <li><a href="customerProfile.php"><i class="fas fa-user"></i>My Profile</a></li>
                <li><a href="myOrder.php"><i class="fas fa-cart-shopping"></i>My Orders</a></li>
                <li><a href="myWishlist.php"><i class="fas fa-heart"></i>My Whislists</a></li>
                <li><a href="myReview.php"><i class="fas fa-money-bill"></i>My Reviews</a></li>
                <li><a href="mycart.php"><i class="fas fa-cart-shopping"></i>My Cart</a></li>
            </ul>
        </div>
        <div class="small-container cart-page">
            <div class="hed">My Cart</div>
            <div class="cart-contain">
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>SubTotal</th>
                    </tr>
                    <?php foreach ($cart_products as $product) { ?>
                    <tr>
                        <td>
                            <div class="cart-info">
                                <a href="../HomePage/productdtl.php?product_id=<?php echo $product['PRODUCT_ID']; ?>">
                                    <?php
                                    $imageBase64 = $db->getProductImage($product['PRODUCT_ID']);
                                    ?>
                                    <?php if (!empty($imageBase64)): ?>
                                    <img src="data:image/jpeg;base64,<?php echo $imageBase64; ?>" alt="Product Image"
                                        class="product-image">
                                    <?php else: ?>
                                    <img src="../Image/apple.jpeg" alt="No Image" class="product-image">
                                    <?php endif; ?>
                                </a>
                                <div>
                                    <p><a href="../HomePage/productdtl.php?product_id=<?php echo $product['PRODUCT_ID']; ?>"
                                            style="font-weight: bold; color: black;"><?php echo $product['PRODUCT_NAME']; ?></a>
                                    </p>
                                    <small class="larger-font">Price: £<?php echo $product['PRICE']; ?></small>
                                    <br>
                                    <form id="removeForm" action="removeFromCart.php" method="post">
                                        <input type="hidden" name="product_id"
                                            value="<?php echo $product['PRODUCT_ID']; ?>">
                                        <input type="hidden" name="cart_id" value="<?php echo $cartid; ?>">
                                        <button type="submit">Remove</button>
                                    </form>

                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="quantity-input">
                                <!-- FontAwesome icons for plus and minus buttons -->
                                <button class="quantity-btn minus"
                                    data-product-id="<?php echo $product['PRODUCT_ID']; ?>"><i
                                        class="fas fa-minus"></i></button>
                                <input id="quantity-<?php echo $product['PRODUCT_ID']; ?>" type="text" class="quantity"
                                    value="<?php echo $product['QUANTITY']; ?>" readonly>
                                <button class="quantity-btn plus"
                                    data-product-id="<?php echo $product['PRODUCT_ID']; ?>"><i
                                        class="fas fa-plus"></i></button>
                            </div>
                        </td>
                        <td style="font-size: 16px;">£<?php echo number_format($product['PRICE'] * $product['QUANTITY'], 2); ?></td>
                    </tr>
                    <?php } ?>
                </table>

                <div class="total-price">
                    <table>
                        <tr>
                            <td>SubTotal</td>
                            <td>£<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td>£<?php echo number_format($tax, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td>£<?php echo number_format($total, 2); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="checkout-button">
                <button onclick="location.href='../paypal/checkout.php'" class="btn">Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <br><br><br><br><br>
    <?php include('../FooterPage/footer.php');?>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to plus and minus buttons
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const quantityField = document.querySelector(`#quantity-${productId}`);
                let newQuantity = parseInt(quantityField.value);

                // Increment or decrement the quantity
                if (this.classList.contains('plus')) {
                    newQuantity++;
                } else {
                    newQuantity = Math.max(newQuantity - 1,
                    1); // Ensure quantity does not go below 1
                }

                // Update the quantity field
                quantityField.value = newQuantity;

                // Send an AJAX request to update the quantity in the database
                const url = 'updateQuantity.php';
                const data = new FormData();
                data.append('cartId', '<?php echo $cartid; ?>');

                data.append('productId', productId);
                data.append('newQuantity', newQuantity);

                fetch(url, {
                        method: 'POST',
                        body: data
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Quantity updated successfully:', data);
                        setTimeout(() => {
                            location.reload(
                            true); // Reload the page without using cache
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error updating quantity:', error);
                    });
            });
        });
    });
    </script>
    <script src="../HeaderPage/head.php"></script>
</body>

</html>
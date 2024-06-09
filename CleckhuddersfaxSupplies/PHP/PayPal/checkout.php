<?php
  require_once '../../partials/dbConnect.php';
  require_once '../alertService.php';

  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }

  $customerid = $_SESSION['user_id'];

  $db = new Database();
  $cartid = $db->getCartIdUsingCustomerId($customerid);
  $cart_products = $db->getCartProducts($cartid);

  $subtotal = 0;
  $totalNoOfItems = 0;
  $totalNoOfProducts = 0;
  foreach ($cart_products as $product) {
      $totalNoOfProducts += 1;
    $totalNoOfItems += $product['QUANTITY'];
    $subtotal += $product['PRICE'] * $product['QUANTITY'];
  }
// validating 20 products limit
  if ($totalNoOfProducts > 20) {
      AlertService::setError("Cannot have more than 20 products in a cart! Please remove some..");
      header("Location: ../CustomerProfilePage/mycart.php");
      exit();
  }

  $total = $subtotal;
  $customer = $db->getCustomerById($customerid);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link rel="stylesheet" href="checkout.css">
</head>

<body>
    <div class="container">
        <h1>Checkout</h1>
        <form action="process_order.php" method="POST">
            <div class="left-container">

                <!-- Billing details -->
                <div class="details-box">
                    <div class="billing-details">
                        <h2>Billing Details</h2>
                        <label for="full-name">Full Name</label>
                        <input type="text" id="full-name" name="full-name"
                            value="<?php echo $customer['FIRST_NAME'] . ' ' . $customer['LAST_NAME'] ?>" disabled>

                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $customer['EMAIL'] ?>" disabled>

                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="<?php echo $customer['ADDRESS'] ?>" disabled>
                    </div>
                </div>

                 <!-- Cart items -->
                <div class="product-box">
                    <h2>Order Products</h2>
                    <?php foreach ($cart_products as $product) { ?>
                    <div class="product-item">
                        <?php
                            $imageBase64 = $db->getProductImage($product['PRODUCT_ID']);
                        ?>
                        <?php if (!empty($imageBase64)): ?>
                        <img src="data:image/jpeg;base64,<?php echo $imageBase64; ?>" alt="Product Image"
                            class="product-image">
                        <?php else: ?>
                        <img src="../Image/apple.jpeg" alt="No Image" class="product-image">
                        <?php endif; ?>
                        <div class="product-details">
                            <div class="product-info">
                                <span><?php echo $product['PRODUCT_NAME']; ?></span>
                            </div>
                            <div class="product-Quan">
                                <span><?php echo $product['QUANTITY']; ?></span>
                            </div>
                            <div class="product-price">
                                <span>£<?php echo number_format($product['PRICE'] * $product['QUANTITY'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

             <!-- Collection slot choosing -->
            <div class="right-container">
                <div class="collection-slot">
                    <h2>Collection Slot</h2>
                    <label for="collection-day">Select a Day</label>
                    <select id="collection_date" name="collection_date" required>
                        <?php
                        for ($i = 1; $i <= 14; $i++) {
                            $date = date('Y-m-d', strtotime("+$i days"));
                            $day_of_week = date('w', strtotime($date));
                            if (in_array($day_of_week, [3, 4, 5])) {
                                echo "<option value='$date'>" . date('l, F j, Y', strtotime($date)) . "</option>";
                            }
                        }
                        ?>
                    </select>

                    <label for="collection_slot">Select a Time Slot:</label>
                    <select id="collection_slot" name="collection_slot" required>
                        <option value="10-13">10:00 - 13:00</option>
                        <option value="13-16">13:00 - 16:00</option>
                        <option value="16-19">16:00 - 19:00</option>
                    </select><br><br>
                 <!-- Order Summary -->
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-item total">
                        <span>Total:</span>
                        <input type="hidden" name="totalprice" value="<?php echo $total; ?>">
                        <input type="hidden" name="qty" value="<?php echo $totalNoOfItems; ?>">
                        <span>£<?php echo $total; ?></span>
                    </div>
                </div>
                <div class="payment-details">
                    <h2>Payment Option</h2>
                    <div class="paypal-option">
                        <label for="paypal">
                            <img src="../Image/PayPal.jpeg" alt="PayPal" class="paypal-logo">
                        </label>
                    </div>
                </div>
                <button type="submit">Place Order</button>
            </div>
        </form>
    </div>
</body>

</html>
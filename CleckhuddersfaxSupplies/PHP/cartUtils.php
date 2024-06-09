<?php
session_start();
require_once '../partials/dbConnect.php';

$existingCart = [];

// Update the db once the user logs in.
if (isset($_COOKIE['cart'])) {
    $db = new Database();
    $conn = $db->getConnection();
    $cartItems = json_decode($_COOKIE['cart'], true);

    try {
        // Prepare the SQL query to fetch cart items for the user
        $cartId = $db->getCartIdUsingCustomerId($_SESSION['user_id']);
        echo '<br>: cart id is' . $cartId . '<br>';
        $query = "SELECT product_id, quantity, special_instruction FROM cart_product WHERE cart_id = :cart_id";

        $statement = oci_parse($conn, $query);
        oci_bind_by_name($statement, ":cart_id", $cartId);
        oci_execute($statement);

        while ($row = oci_fetch_assoc($statement)) {
            $existingCart[$row['PRODUCT_ID']] = array(
                'quantity' => $row['QUANTITY'],
                'special_instruction' => $row['SPECIAL_INSTRUCTION']
            );
        }

        // Loop through items from the cookies and update or insert them into the database
        foreach ($cartItems as $product_id => $item) {
            $quantity = $item['quantity'];
            $special_instruction = isset($item['special_instruction']) ? $item['special_instruction'] : '';

            // Check if the product already exists in the cart
            if (isset($existingCart[$product_id])) {
                // Product exists, update its quantity and special instruction
                $existingCart[$product_id]['quantity'] += $quantity;
                $existingCart[$product_id]['special_instruction'] = $special_instruction;

                // Update the cart item in the database
                if ($db->updateCartItem($_SESSION['user_id'], $product_id, $existingCart[$product_id]['quantity'], $existingCart[$product_id]['special_instruction'])) {
                    echo 'Cart item updated in database.';
                } else {
                    echo 'Failed to update cart item in database.';
                }
            } else {
                // Product doesn't exist, insert it into the cart
                if ($db->insertCartItem($_SESSION['user_id'], $product_id, $quantity, $special_instruction)) {
                    echo 'New cart item inserted into database.';
                } else {
                    echo 'Failed to insert new cart item into database.';
                }
            }
        }
        oci_commit($conn);
        // Clear the cookie after updating the database
        setcookie('cart', '', time() - 3600, "/");

    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        $db->closeConnection();
    }
}
header("Location: HomePage/homepage.php");
?>

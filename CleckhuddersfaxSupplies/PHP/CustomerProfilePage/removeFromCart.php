<?php

    // Remove an product from card
    require_once '../../partials/dbConnect.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $db = new Database();
        $customer_id = $_SESSION['user_id'];
        $cartId = $_POST['cart_id'];
        
        $success = $db->removeProductFromCart($cartId, $product_id);

        if($success) {
            header("Location: mycart.php");
            exit;
        } else {
            echo "Failed to remove the product from the cart.";
        }
    } else {
        echo "Invalid product ID.";
    }
?>

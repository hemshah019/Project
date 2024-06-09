<?php
session_start();
include '../../partials/dbConnect.php';

$product_id = $_GET['productid'];
$quantity = isset($_GET['quantity']) ? $_GET['quantity'] : 1;
$special_instruction = isset($_GET['special_instruction']) ? $_GET['special_instruction'] : '';

$db = new Database();
$conn = $db->getConnection();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    // If the user is logged in then update the database.
    $user_id = $_SESSION['user_id'];
    $cart_id = $db->getCartIdUsingCustomerId($user_id);

    $query = "SELECT quantity, special_instruction FROM cart_product WHERE cart_id = :cart_id AND product_id = :product_id";

    // Prepare the statement
    $statement = oci_parse($conn, $query);

    oci_bind_by_name($statement, ":cart_id", $cart_id);
    oci_bind_by_name($statement, ":product_id", $product_id);

    // Execute the statement
    oci_execute($statement);

    // Fetch the row
    $existingCartItem = oci_fetch_assoc($statement);

    if ($existingCartItem) {
        // Product exists, update its quantity and special instruction
        $existingQuantity = $existingCartItem['QUANTITY'];
        $existingSpecialInstruction = $existingCartItem['SPECIAL_INSTRUCTION'];
        $newQuantity = $existingQuantity + $quantity;

        if ($db->updateCartItem($user_id, $product_id, $newQuantity, $special_instruction)) {
            echo 'Item updated in cart in database.';
        } else {
            echo 'Failed to update item in cart in database.';
        }
    } else {
        // Product doesn't exist, insert it into the cart
        if ($db->insertCartItem($user_id, $product_id, $quantity, $special_instruction)) {
            echo 'Item added to cart in database.';
        } else {
            echo 'Failed to add item to cart in database.';
        }
    }

} else {

    // If the user is not logged in, add the item to the cart in cookies
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
    } else {
        $cart = array();
    }

    if (isset($cart[$product_id])) {
        $cart[$product_id]['quantity'] += $quantity;
        $cart[$product_id]['special_instruction'] = $special_instruction;
    } else {
        $cart[$product_id] = array(
            'quantity' => $quantity,
            'special_instruction' => $special_instruction
        );
    }
    // Save the updated cart in the cookie 30 days expiration
    setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");

    // To-be deleted
    if (isset($_COOKIE['cart'])) {
        var_dump($_COOKIE['cart']);
    } else {
        echo 'Cookie "cart" not set.';
    }
    echo 'Item added to cart in cookies.';
}
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>

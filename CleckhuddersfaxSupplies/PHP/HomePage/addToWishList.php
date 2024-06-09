<?php
session_start(); 
include '../../partials/dbConnect.php';
include '../alertService.php';

$db = new Database();

if(!isset($_SESSION['isAuthenticated']) && !isset($_SESSION['loggedin'])) {
    AlertService::setError('Please log in first in order to add to wish list!');
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$productId = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($productId === 0) {
    echo "Error: Product ID is missing or invalid.";
    exit;
}

// Check if customer ID exists in session
if (!isset($_SESSION['user_id'])) {
    echo "Error: Customer ID not found in session.";
    exit;
}

$customerId = $_SESSION['user_id']; // Get customer ID from session

$result = $db->addToWishlist($productId, $customerId); // Call addToWishlist method with product ID and customer ID
header("Location: " . $_SERVER['HTTP_REFERER']);

?>

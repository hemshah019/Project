<?php

include '../../partials/dbConnect.php';

$db = new Database();

// Save a new review.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customerId'];
    $productId = $_POST['productId'];
    $rating = $_POST['rating'] + 0;
    $comments = $_POST['comments'];
    if ($rating !=1 && $rating !=2 && $rating !=3 && $rating !=4 && $rating !=5 && $rating = 0) {
        $rating = 1;
    }
    $success = $db->addReview($customerId, $productId, $rating, $comments);
} 
?>

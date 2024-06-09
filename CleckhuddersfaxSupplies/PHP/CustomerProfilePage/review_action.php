<?php
include '../../partials/dbConnect.php';
$db = new Database();

    // Delete a review
    if (isset($_GET['delete_reviewId'])) {
        $reviewId = intval($_GET['delete_reviewId']);
        $db->deleteReviewByReviewId($reviewId);
        header("Location: myReview.php");
        exit();
    }

    // Update a review comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_reviewId']) && isset($_POST['editedComment'])) {
        $reviewId = $_POST['edit_reviewId'];
        $editedComment = $_POST['editedComment'];

        $query = "UPDATE review SET comments = :editedComment WHERE review_id = :reviewId";
        $conn = $db->getConnection();
        $statement = oci_parse($conn, $query);

        oci_bind_by_name($statement, ":editedComment", $editedComment);
        oci_bind_by_name($statement, ":reviewId", $reviewId);
        $success = oci_execute($statement);

        if ($success) {
            header("Location: myReview.php");
            exit();
        } else {
            echo "Failed to update review.";
        }
    } else {
        echo "Invalid request.";
    }

?>


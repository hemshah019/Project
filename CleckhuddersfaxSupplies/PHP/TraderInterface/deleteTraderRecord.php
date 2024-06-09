<?php
include '../../partials/dbConnect.php';
$db = new Database();



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entityType = $_POST['type'];
    $entityID = intval($_POST['id']); // Convert to integer

    // Connect to the Oracle database
    $conn = $db->getConnection();
    if (!$conn) {
        $e = oci_error();
        echo "Connection failed";
        exit;
    }

    try {
        // Start a transaction
        oci_execute(oci_parse($conn, 'BEGIN'), OCI_NO_AUTO_COMMIT);

        if ($entityType === 'product') {
            $sqlFavourite = "DELETE FROM FAVOURITE WHERE Product_Id = :entity_id";
            $stidFavourite = oci_parse($conn, $sqlFavourite);
            oci_bind_by_name($stidFavourite, ':entity_id', $entityID);
            if (!oci_execute($stidFavourite, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from FAVOURITE");
            }
            
            $sqlReview = "DELETE FROM REVIEW WHERE Product_Id = :entity_id";
            $stidReview = oci_parse($conn, $sqlReview);
            oci_bind_by_name($stidReview, ':entity_id', $entityID);
            if (!oci_execute($stidReview, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Review");
            }

            $sqlOrderProduct = "DELETE FROM ORDER_PRODUCT WHERE Product_ID = :entity_id";
            $stidOrderProduct = oci_parse($conn, $sqlOrderProduct);
            oci_bind_by_name($stidOrderProduct, ':entity_id', $entityID);
            if (!oci_execute($stidOrderProduct, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from OrderProduct");
            }
            
            // Delete the product record from the Product table
            $sqlProduct = "DELETE FROM PRODUCT WHERE Product_Id = :entity_id";
            $stidProduct = oci_parse($conn, $sqlProduct);
            oci_bind_by_name($stidProduct, ':entity_id', $entityID);
            if (!oci_execute($stidProduct, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Product");
            }

            echo "Product deleted successfully!";

        }
        elseif ($entityType === 'shop') {
            $sqlShop = "DELETE FROM SHOP WHERE Shop_Id = :entity_id";
            $stidShop = oci_parse($conn, $sqlShop);
            oci_bind_by_name($stidShop, ':entity_id', $entityID);
            if (!oci_execute($stidShop, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Shop");
            }

            echo "Shop deleted successfully!";
        }
        else {
            throw new Exception("Invalid entity type specified");
        }

        // Commit the transaction
        oci_commit($conn);
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        oci_rollback($conn);
        echo "Transaction failed: " . $e->getMessage();
    } finally {
        // Free the statement identifiers if they are initialized
        if (isset($stidProduct)) oci_free_statement($stidProduct);
        if (isset($stidShop)) oci_free_statement($stidShop);
        // if (isset($stidTrader)) oci_free_statement($stidTrader);
        // if (isset($stidOrder)) oci_free_statement($stidOrder);
        if (isset($stidOrderProduct)) oci_free_statement($stidOrderProduct);
        if (isset($stidReview)) oci_free_statement($stidReview);
        if (isset($stidFavourite)) oci_free_statement($stidFavourite);

        // Close the Oracle connection
        oci_close($conn);
    }
}
?>

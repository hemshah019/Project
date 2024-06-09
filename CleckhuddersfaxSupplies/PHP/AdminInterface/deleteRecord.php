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

        if ($entityType === 'trader') {
            // Delete from FAVOURITE where Product_ID in (select Product_ID from Product where Shop_ID in (select Shop_ID from Shop where Trader_ID matches))
            $sqlFavourite = "DELETE FROM FAVOURITE WHERE Product_ID IN (SELECT Product_ID FROM PRODUCT WHERE Shop_ID IN (SELECT Shop_ID FROM SHOP WHERE Trader_ID = :entity_id))";
            $stidFavourite = oci_parse($conn, $sqlFavourite);
            oci_bind_by_name($stidFavourite, ':entity_id', $entityID);
            if (!oci_execute($stidFavourite, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from FAVOURITE");
            }

            // Delete from Review where Product_ID in (select Product_ID from Product where Shop_ID in (select Shop_ID from Shop where Trader_ID matches))
            $sqlReview = "DELETE FROM REVIEW WHERE Product_ID IN (SELECT Product_ID FROM PRODUCT WHERE Shop_ID IN (SELECT Shop_ID FROM SHOP WHERE Trader_ID = :entity_id))";
            $stidReview = oci_parse($conn, $sqlReview);
            oci_bind_by_name($stidReview, ':entity_id', $entityID);
            if (!oci_execute($stidReview, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Review");
            }

            // Delete from OrderProduct where Product_ID in (select Product_ID from Product where Shop_ID in (select Shop_ID from Shop where Trader_ID matches))
            $sqlOrderProduct = "DELETE FROM ORDER_PRODUCT WHERE Product_ID IN (SELECT Product_ID FROM PRODUCT WHERE Shop_ID IN (SELECT Shop_ID FROM SHOP WHERE Trader_ID = :entity_id))";
            $stidOrderProduct = oci_parse($conn, $sqlOrderProduct);
            oci_bind_by_name($stidOrderProduct, ':entity_id', $entityID);
            if (!oci_execute($stidOrderProduct, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from OrderProduct");
            }

            // Delete from Product via Shop
            $sqlProduct = "DELETE FROM Product WHERE Shop_ID IN (SELECT Shop_ID FROM Shop WHERE Trader_ID = :entity_id)";
            $stidProduct = oci_parse($conn, $sqlProduct);
            oci_bind_by_name($stidProduct, ':entity_id', $entityID);
            if (!oci_execute($stidProduct, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Product");
            }

            // Delete from Shop
            $sqlShop = "DELETE FROM Shop WHERE Trader_ID = :entity_id";
            $stidShop = oci_parse($conn, $sqlShop);
            oci_bind_by_name($stidShop, ':entity_id', $entityID);
            if (!oci_execute($stidShop, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Shop");
            }

            // Finally, delete the trader record from the Trader table
            $sqlTrader = "DELETE FROM Trader WHERE Trader_ID = :entity_id";
            $stidTrader = oci_parse($conn, $sqlTrader);
            oci_bind_by_name($stidTrader, ':entity_id', $entityID);
            if (!oci_execute($stidTrader, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Trader");
            }

            echo "Trader deleted successfully!";

        } elseif ($entityType === 'product') {
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

        } elseif ($entityType === 'customer') {
            $sqlCartProduct = "DELETE FROM cart_product WHERE Cart_ID = (SELECT cart_id FROM cart WHERE customer_id = :entity_id)";
            $sqlCartProduct = oci_parse($conn, $sqlCartProduct);
            oci_bind_by_name($sqlCartProduct, ':entity_id', $entityID);
            if (!oci_execute($sqlCartProduct, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from CartProduct");
            }
            
            // Delete from Favourite where Customer_ID matches
            $sqlCart = "DELETE FROM cart WHERE Customer_ID = :entity_id";
            $sqlCart = oci_parse($conn, $sqlCart);
            oci_bind_by_name($sqlCart, ':entity_id', $entityID);
            if (!oci_execute($sqlCart, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Cart");
            }

            $sqlFavourite = "DELETE FROM Favourite WHERE Customer_ID = :entity_id";
            $stidFavourite = oci_parse($conn, $sqlFavourite);
            oci_bind_by_name($stidFavourite, ':entity_id', $entityID);
            if (!oci_execute($stidFavourite, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Favourite");
            }

            // Delete from Review where Customer_ID matches
            $sqlReview = "DELETE FROM Review WHERE Customer_ID = :entity_id";
            $stidReview = oci_parse($conn, $sqlReview);
            oci_bind_by_name($stidReview, ':entity_id', $entityID);
            if (!oci_execute($stidReview, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Review");
            }

            // Handle dependent records first (e.g., orders related to this customer)
            // Delete from OrderProduct where the Order IDs are related to the Customer ID
            $sqlOrderProduct = "DELETE FROM ORDER_PRODUCT WHERE Order_ID IN (SELECT Order_ID FROM Orders WHERE Customer_ID = :entity_id)";
            $stidOrderProduct = oci_parse($conn, $sqlOrderProduct);
            oci_bind_by_name($stidOrderProduct, ':entity_id', $entityID);
            if (!oci_execute($stidOrderProduct, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from OrderProduct");
            }

            // Delete from Orders where Customer_ID matches
            $sqlOrder = "DELETE FROM Orders WHERE Customer_ID = :entity_id";
            $stidOrder = oci_parse($conn, $sqlOrder);
            oci_bind_by_name($stidOrder, ':entity_id', $entityID);
            if (!oci_execute($stidOrder, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Orders");
            }

            // Delete the customer record from the Customer table
            $sqlCustomer = "DELETE FROM Customer WHERE Customer_ID = :entity_id";
            $stidCustomer = oci_parse($conn, $sqlCustomer);
            oci_bind_by_name($stidCustomer, ':entity_id', $entityID);
            if (!oci_execute($stidCustomer, OCI_NO_AUTO_COMMIT)) {
                throw new Exception("Error deleting from Customer");
            }

            echo "Customer deleted successfully!";
        } else {
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
        if (isset($sqlCartProduct)) oci_free_statement($sqlCartProduct);
        if (isset($sqlCart)) oci_free_statement($sqlCart);
        if (isset($stidShop)) oci_free_statement($stidShop);
        if (isset($stidTrader)) oci_free_statement($stidTrader);
        if (isset($stidCustomer)) oci_free_statement($stidCustomer);
        if (isset($stidOrder)) oci_free_statement($stidOrder);
        if (isset($stidOrderProduct)) oci_free_statement($stidOrderProduct);
        if (isset($stidReview)) oci_free_statement($stidReview);
        if (isset($stidFavourite)) oci_free_statement($stidFavourite);
        // Close the Oracle connection
        oci_close($conn);
    }
}
?>

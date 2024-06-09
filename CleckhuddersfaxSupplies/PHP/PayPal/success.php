<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../partials/dbConnect.php';
require_once '../MailerService.php';
require_once 'orderReceipt.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['date']) && isset($_GET['slot'])) {
    $collection_date = $_GET['date'];
    $collection_slot = $_GET['slot'];
    $total_price = $_GET['amount'] ?? null;
    $customer_username = $_GET['cusername'] ?? null;
    $customer_id = $db->getCustomerIdUsingUsername($customer_username);
    $no_of_items = $_GET['qty'] ?? null;
    global $cartid;
    $cartid = $db->getCartIdUsingCustomerId($customer_id);
    $order_status = "created";
    $orderId = null;
    $customer_email = $db->getEmailByCustomerId($customer_id);


    // Insert order into the orders table
    $sql = "INSERT INTO orders (order_date, order_status, no_of_items, collection_date, collection_slot, customer_id, total_price) 
        VALUES (CURRENT_TIMESTAMP, :order_status, :no_of_items, TO_DATE(:collection_date, 'YYYY-MM-DD'), :collection_slot, :customer_id, :total_price)
        RETURNING order_id INTO :order_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':order_status', $order_status);
    oci_bind_by_name($stmt, ':no_of_items', $no_of_items);
    oci_bind_by_name($stmt, ':collection_date', $collection_date);
    oci_bind_by_name($stmt, ':collection_slot', $collection_slot);
    oci_bind_by_name($stmt, ':customer_id', $customer_id);
    oci_bind_by_name($stmt, ':total_price', $total_price);
    oci_bind_by_name($stmt, ':order_id', $orderId, 32);
    oci_execute($stmt);
    oci_free_statement($stmt);

    // Update slot count in the slots table
    $sql = "MERGE INTO collection_slot s
            USING (SELECT TO_DATE(:collection_date, 'YYYY-MM-DD') AS collection_date, :collection_slot AS collection_slot FROM dual) d
            ON (s.collection_date = d.collection_date AND s.collection_slot = d.collection_slot)
            WHEN MATCHED THEN
                UPDATE SET orders_count = orders_count + 1
            WHEN NOT MATCHED THEN
                INSERT (collection_date, collection_slot, orders_count) VALUES (d.collection_date, d.collection_slot, 1)";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':collection_date', $collection_date);
    oci_bind_by_name($stmt, ':collection_slot', $collection_slot);
    oci_execute($stmt);
    oci_free_statement($stmt);


    // Updating order products from cart products
    $cart_products = $db->getCartProducts($cartid);
    sendCartOrderReceipt($customer_email, $cart_products, $orderId);
    echo $customer_email;

    foreach ($cart_products as $product) {
        $productStock = $db->getProductStock($product['PRODUCT_ID']);
        // $newStock = $productStock - $no_of_items;
        $newStock = $productStock - $product['QUANTITY'];
        $db->updateProductStock($product['PRODUCT_ID'], $newStock);
        $query = "INSERT INTO order_product (quantity, product_id, order_id) VALUES (:quantity, :product_id, :order_id)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':quantity', $product['QUANTITY']);
        oci_bind_by_name($stmt, ':product_id', $product['PRODUCT_ID']);
        oci_bind_by_name($stmt, ':order_id', $orderId);
        oci_execute($stmt);
        oci_free_statement($stmt);
        $SCC = $db->removeProductFromCart($cartid, $product['PRODUCT_ID']);
    }

    sendIndividualReceipts($orderId);
    oci_close($conn);

    echo 'Order placed and payment successful!';
    header("Location: ../CustomerProfilePage/myOrder.php");
} else {
    echo 'Payment details not received.';
}
?>
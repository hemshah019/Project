<?php
    require_once '../MailerService.php';
    require_once '../../partials/dbConnect.php';

    // send receipt to customer.
    function sendCartOrderReceipt($email, $cartProducts, $orderid) {
        $db = new Database();
        $total = 0;
        foreach ($cartProducts as $product) {
            $total += $product['PRICE'] * $product['QUANTITY'];
        }
        $order = $db->getOrderByOrderId($orderid);

        $orderDetails = $cartProducts;
        $orderDetails['total'] = $total;
        $orderDetails['collection_date'] = $order['COLLECTION_DATE'];
        $orderDetails['collection_slot'] = $order['COLLECTION_SLOT'];

        $mailerService = new MailerService();
        return $mailerService->sendOrderReceipt($email, $orderDetails);
    }



    // Send receipt to individual trader.
    function sendIndividualReceipts($orderId) {
        $db = new Database(); 
        $conn = $db->getConnection();

        $traderEmail = null;
    
        echo 'inside this function. The order id: ' . $orderId . '<br>';
    
        // Query to fetch order details
        $query = "SELECT o.order_id, o.customer_id, o.order_date, op.quantity, p.price, p.product_name, tr.username, tr.email, c.first_name ||' '|| c.last_name as Customer_Name, c.username AS customer_username
                  FROM orders o
                  JOIN order_product op ON o.order_id = op.order_id
                  JOIN product p ON op.product_id = p.product_id
                  JOIN shop s ON s.shop_id = p.shop_id
                  JOIN trader tr ON tr.trader_id = s.trader_id
                  JOIN customer c ON o.customer_id = c.customer_id
                  WHERE o.order_id = :order_id";
    
        $statement = oci_parse($conn, $query);
        oci_bind_by_name($statement, ":order_id", $orderId);
        oci_execute($statement);
    
        // Organize order details by trader
        $ordersByTrader = array();
        while ($row = oci_fetch_assoc($statement)) {
            $traderEmail = $row['EMAIL'];
            if (!isset($ordersByTrader[$traderEmail])) {
                $ordersByTrader[$traderEmail] = array();
            }
            $ordersByTrader[$traderEmail][] = $row;
        }
    
        // Send individual receipts to each trader
        foreach ($ordersByTrader as $traderEmail => $orderItems) {
            // Extract trader details
            $traderUsername = $orderItems[0]['USERNAME'];
            $customerName = $orderItems[0]['CUSTOMER_NAME'];
    
            // Prepare order details for the trader
            $orderDetailsForTrader = array();
            foreach ($orderItems as $orderItem) {
                $orderDetailsForTrader[] = array(
                    'PRODUCT_NAME' => $orderItem['PRODUCT_NAME'],
                    'PRICE' => $orderItem['PRICE'],
                    'QUANTITY' => $orderItem['QUANTITY']
                );
            }
    
            // Send email to trader
            $mailerService = new MailerService();
            $success = $mailerService->sendTraderOrderReceipt($traderEmail, $orderDetailsForTrader, $customerName, $traderUsername);
            if (!$success) {
                // Log or handle failure to send email to the trader
                echo 'Failed to send email to trader: ' . $traderEmail . '<br>';
            }
        }
    }
?>

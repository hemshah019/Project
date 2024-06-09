<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../partials/dbConnect.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $collection_date = $_POST['collection_date'];
    $collection_slot = $_POST['collection_slot'];
    $totalprice = $_POST['totalprice'];
    $qty = $_POST['qty'];
    $customer_username = $_SESSION['username'];

    $order_time = time();

    // Extract start hour from collection slot
    $collection_start_hour = explode('-', $collection_slot)[0];
    $collection_start_time = strtotime($collection_date . ' ' . $collection_start_hour . ':00'); 
    //minimum 24hrs
    $minimum_order_time = $order_time + (24 * 60 * 60); 

    if ($collection_start_time < $minimum_order_time) {
        die('Collection slot must be at least 24 hours after placing the order.');
    }

    if (!($db->isValidSlot($collection_date, $collection_slot))) {
        die('Selected slot is full.');
    }


    // Redirect to PayPal for payment
    $paypalURL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    $paypalID = 'sb-xuixj30917476@business.example.com'; 
    $item_name = "Order from " . $customer_username;
    $amount = $totalprice;
    $return_url = 'http://localhost/project/CleckhuddersfaxSupplies/PHP/PayPal/success.php?date='. $collection_date .'&slot=' .$collection_slot . '&cusername='
                    .$customer_username . '&amount=' . $amount . '&qty=' . $qty;

    echo "<form id='paypalForm' action='$paypalURL' method='post'>
            <input type='hidden' name='business' value='$paypalID'>
            <input type='hidden' name='cmd' value='_xclick'>
            <input type='hidden' name='item_name' value='$item_name'>
            <input type='hidden' name='amount' value='$amount'>
            <input type='hidden' name='currency_code' value='GBP'>
            <input type='hidden' name='return' value='$return_url'>
            <input type='hidden' name='cancel_return' value='http://localhost/project/CleckhuddersfaxSupplies/PHP/PayPal/cancel.php'>
          </form>
          <script type='text/javascript'>
            document.getElementById('paypalForm').submit();
          </script>";
}
?>

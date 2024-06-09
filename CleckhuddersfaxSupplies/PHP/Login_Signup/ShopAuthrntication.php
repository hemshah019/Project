<?php
session_start();
include '../../partials/dbConnect.php';
include '../alertService.php';  

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $shopName = $_POST['shop-name'];
    $phoneNumber = $_POST['phone-number'];
    $email = $_POST['shop-email'];
    $description = $_POST['description'];
    $traderId = $_SESSION['trader_id'];

    $query = "SELECT * FROM SHOP WHERE SHOP_NAME = :shopName";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':shopName', $shopName);
    oci_execute($stmt);
    if ($row = oci_fetch_assoc($stmt)) {
        AlertService::setError('A shop with the same name already exists.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    oci_free_statement($stmt);

    $query = "SELECT * FROM SHOP WHERE SHOP_EMAIL = :email";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':email', $email);
    oci_execute($stmt);
    if ($row = oci_fetch_assoc($stmt)) {
        AlertService::setError('A shop with the same email already exists.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    oci_free_statement($stmt);

    $query = "SELECT * FROM SHOP WHERE PHONE = :phoneNumber";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':phoneNumber', $phoneNumber);
    oci_execute($stmt);
    if ($row = oci_fetch_assoc($stmt)) {
        AlertService::setError('A shop with the same phone number already exists.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    oci_free_statement($stmt);

    $insertQuery = "INSERT INTO SHOP(SHOP_IMAGE, SHOP_NAME, REGISTRATION_DATE, DESCRIPTION, SHOP_EMAIL, PHONE, STATUS, TRADER_ID)
    VALUES (empty_blob(), :shopName, SYSDATE, :description, :email, :phoneNumber, 'INACTIVE', :traderId)";
    
    $insertStmt = oci_parse($conn, $insertQuery);
    oci_bind_by_name($insertStmt, ':shopName', $shopName);
    oci_bind_by_name($insertStmt, ':description', $description);
    oci_bind_by_name($insertStmt, ':email', $email);
    oci_bind_by_name($insertStmt, ':phoneNumber', $phoneNumber);
    oci_bind_by_name($insertStmt, ':traderId', $traderId);

    $result = oci_execute($insertStmt);

    if ($result) {
        oci_commit($conn);
        unset($_SESSION['trader_id']);
        AlertService::setSuccess('Your Trader Account and Shop have been successfully registered with us. <br> 
                        Please wait for Admin verification. This process typically takes 1-2 business days.<br> 
                        We will notify you via email once the admin verifies your account.<br>
                        If you have any questions or need assistance, don\'t hesitate to contact our support team at cleckhuddedrsfaxsupplies.com.');
        header("Location: login.php");
    } else {
        $error = oci_error($insertStmt);
        echo "Error inserting data: " . $error['message'];
    }

    oci_free_statement($insertStmt);
    oci_close($conn);
}
?>
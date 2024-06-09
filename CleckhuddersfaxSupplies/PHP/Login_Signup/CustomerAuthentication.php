<?php

include '../../partials/dbConnect.php';
require_once '../MailerService.php';
include '../alertService.php';

$db = new Database();
$conn = $db->getConnection();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if(isset($_POST['submit']))
{
    // Retrieve form data 
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $number = $_POST['phoneNumber'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $gender = $_POST["gender"];
    $Uname = $_POST['userName'];
    $cpassword = $_POST['confirmPassword'];

    // Set the data into session for further use
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['password'] = $_POST['password'];
    $_SESSION['fname'] = $_POST['firstName'];
    $_SESSION['lname'] = $_POST['lastName'];
    $_SESSION['number'] = $_POST['phoneNumber'];
    $_SESSION['address'] = $_POST['address'];
    $_SESSION['age'] = $_POST['age'];
    $_SESSION['gender'] = $_POST['gender'];
    $_SESSION['Uname'] = $_POST['userName'];


    if (strlen($password) < 8 || strlen($password) > 32) {
        $_SESSION['error'] = "Password must be at least 8 or less than 32 characters long.";
        $_SESSION['form_data'] = $_POST;
        header("Location: customerSignup.php");
        exit();
    }
    if ($password !== $cpassword) {
        $_SESSION['error'] = "Password and Confirm Password do not match. Please try again.";
        $_SESSION['form_data'] = $_POST;
        header("Location: customerSignup.php");
        exit();
    }
    if (!preg_match('/[!@#$%^&*()\-_=+]/', $password)) {
        $_SESSION['error'] = "Password must contain at least one special character.";
        $_SESSION['form_data'] = $_POST;
        header("Location: customerSignup.php");
        exit();
    }

    if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
        $_SESSION['error'] = "Password must have at least one uppercase letter, one lowercase letter, and a number.";
        $_SESSION['form_data'] = $_POST;
        header("Location: customerSignup.php");
        exit();
    }

    // Check if email, username, or contact number already exists
    $query_check = "SELECT Email, Username, Phone FROM Customer WHERE Email = '$email' OR Username = '$Uname' OR Phone = '$number'";
    $statement_check = oci_parse($conn, $query_check);
    oci_execute($statement_check);
    $row = oci_fetch_assoc($statement_check);

    if ($row !== false) {
        $message = "Email, username, or phone number already exists. Please use different ones.";
        if ($email === $row['EMAIL']) {
            $message = "Email already exists. Please use a different email.";
        } elseif ($Uname === $row['USERNAME']) {
            $message = "Username already exists. Please use a different username.";
        } elseif ($number === $row['PHONE']) {
            $message = "Phone number already exists. Please use a different one.";
        }
        $_SESSION['error'] = $message;
        $_SESSION['form_data'] = $_POST; 
        header("Location: customerSignup.php");
        exit();
    }

    $custSignupOtp = generateOTP();
    $_SESSION['custSignupOtp'] = $custSignupOtp;
    $_SESSION['isVerifiedCustSignupOtp'] = FALSE;

    $mailer = new MailerService();

    // send otp in email
    if ($mailer->signupEmailVerification($email, $custSignupOtp)) {
        AlertService::setWarning('Check your email and enter the OTP.');
        header("Location: ../VerificationPage/otpVerification.php");
        exit;
    } else {
        echo "Failed to send verification email.";
    }
    oci_close($conn);
}


if(isset($_SESSION['isVerifiedCustSignupOtp']) && ($_SESSION['isVerifiedCustSignupOtp'] == TRUE))
{
    $Uname = $_SESSION['Uname'];
    $email = $_SESSION['email'];
    $hpassword = md5($_SESSION['password']);// Password Hahing using md5 
    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
    $number = $_SESSION['number'];
    $address = $_SESSION['address'];
    $age = $_SESSION['age'];
    $gender = $_SESSION['gender'];
    $customerid = null;


    $query = "INSERT INTO Customer (Cust_image, First_Name, Last_Name, Address, Age, Email, Phone, Gender, Username, Password, Registration_Date) 
            VALUES (empty_blob(), '$fname', '$lname', '$address', '$age', '$email', '$number', '$gender', '$Uname', '$hpassword', SYSDATE)
            RETURNING customer_id INTO :customerid";

    $statement = oci_parse($conn, $query);
    oci_bind_by_name($statement, ':customerid', $customerid, 32);
    $result = oci_execute($statement);
    oci_free_statement($statement);


    // Auto create a cart for new customer.
    $cartQuery = "INSERT INTO cart(customer_id) VALUES('$customerid')";
    $statementcart = oci_parse($conn, $cartQuery);
    oci_execute($statementcart);


    if($result) {
        oci_commit($conn);
        AlertService::setSuccess('Successfully created your account. You can log in now!');
        unset($_SESSION['isVerifiedCustSignupOtp']);
        unset($_SESSION['custSignupOtp']);
        header("Location: ../Login_Signup/login.php");
        exit(); 
    }
    else {
        AlertService::setError('Error while creating your account! Try Again');
        $error = oci_error($statement);
    }
    unset($_SESSION['isVerifiedCustSignupOtp']);
    session_destroy();
    oci_close($conn);

}    

function generateOTP($length = 6)
{
    return rand(pow(10, $length - 1), pow(10, $length) - 1);
}
?>
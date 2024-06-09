
<?php
include '../../partials/dbConnect.php';
require_once '../MailerService.php';
include '../alertService.php';
session_start();

$db = new Database();
$conn = $db->getConnection();

$customerId = $_SESSION['user_id'];
$primaryEmail = $db->getEmailByCustomerId($customerId);


if(isset($_POST['update-profile']))
{
    $updatedData = array();
    if (!empty($_POST['first-name'])) {
        $updatedData['FIRST_NAME'] = $_POST['first-name'];
    }
    if (!empty($_POST['last-name'])) {
        $updatedData['LAST_NAME'] = $_POST['last-name'];
    }
    if (!empty($_POST['username'])) {
        $updatedData['USERNAME'] = $_POST['username'];
    }
    if (!empty($_POST['email'])) {
        $updatedData['EMAIL'] = $_POST['email'];
    }
    if (!empty($_POST['address'])) {
        $updatedData['ADDRESS'] = $_POST['address'];
    }
    if (!empty($_POST['phone-number'])) {
        $updatedData['PHONE'] = $_POST['phone-number'];
    }
    if (!empty($_POST['age'])) {
        $updatedData['AGE'] = $_POST['age'];
    }
    if (!empty($_POST['gender'])) {
        $updatedData['GENDER'] = $_POST['gender'];
    }
    if (!empty($_POST['password'])) {
        $updatedData['PASSWORD'] = $_POST['password'];
    }

    if (!empty($updatedData)) 
    {
        $_SESSION['updatedData'] = $updatedData;
        $custUpdateOtp = generateOTP();
        $_SESSION['custUpdateOtp'] = $custUpdateOtp;
        $_SESSION['isVerifiedCustUpdateOtp'] = FALSE;

        $mailer = new MailerService();

        if ($mailer->custProfileUpdateEmailVerification($primaryEmail, $custUpdateOtp)) {
            AlertService::setWarning('Check your email and enter the OTP.');
            header("Location: ../VerificationPage/otpVerification.php");
            exit;
        } else {
            echo "Failed to send verification email.";
        }
        oci_close($conn);
    }
}

if(isset($_SESSION['isVerifiedCustUpdateOtp']) && ($_SESSION['isVerifiedCustUpdateOtp'] == TRUE))
{
    $updatedData = $_SESSION['updatedData'];
    if (!empty($updatedData)) {
        $result = $db->updateCustomerProfile($customerId, $updatedData);
        if ($result) {
            header("Location: customerProfile.php");
            unset($_SESSION['updatedData']);
            unset($_SESSION['isVerifiedCustUpdateOtp']);
            unset($_SESSION['custUpdateOtp']);
            oci_close($conn);
            echo "Profile updated successfully.";
            exit();
        } else {
            echo "Failed to update profile.";
        }
    }

}

function generateOTP($length = 6)
{
    return rand(pow(10, $length - 1), pow(10, $length) - 1);
}
?>

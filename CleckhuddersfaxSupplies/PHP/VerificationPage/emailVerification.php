<?php
  session_start();
  require_once '../MailerService.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') 
  {
    $email = htmlspecialchars(trim($_POST['email']));

    $otp = generateOTP();
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;
    $_SESSION['isVerifiedCustResetOtp'] = FALSE;

    $mailer = new MailerService();

    if ($mailer->forgotPassEmailVerification($email, $otp)) {
        header("Location: otpVerification.php");
        exit;
    } else {
        echo "Failed to send verification email.";
    }
  }

  function generateOTP($length = 6)
  {
      return rand(pow(10, $length - 1), pow(10, $length) - 1);
  }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="emailVerification.css">
</head>

<body>
    <div class="cont-emvf">
        <h2>Verify Your Email</h2>
        <p>Please enter your email address for verification.</p>
        <form action="emailVerification.php" method="POST">
            <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            <button type="submit">VERIFY</button>
        </form>
    </div>
</body>

</html>
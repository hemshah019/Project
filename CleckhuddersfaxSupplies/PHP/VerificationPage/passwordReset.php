<?php
session_start();
$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password1 = htmlspecialchars(trim($_POST['password1']));
    $password2 = htmlspecialchars(trim($_POST['password2']));

    // Password validation
    if (strlen($password1) < 8 || strlen($password1) > 32) {
        $error = "Password must be at least 8 or less than 32 characters long.";
    }
    elseif ($password1 !== $password2) {
      $error = "Password and Confirm Password do not match. Please try again.";
    }
    elseif (!preg_match('/[!@#$%^&*()\-_=+]/', $password1)) {
        $error = "Password must contain at least one special character.";
    }
    elseif (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password1)) {
        $_SESSION['error'] = "Password must have at least one uppercase letter, one lowercase letter, and a number.";
        $_SESSION['form_data'] = $_POST;
        header("Location: customerSignup.php");
        exit();
    } else {
        try {
            require_once '../../partials/dbConnect.php';
            $db = new Database();
            $db->updatePassword($email, $password1);
            echo '<script>alert("Password updated successfully. You will now be redirected to login."); window.location.href="../Login_Signup/login.php";</script>';
            exit();
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
    // Display error message if any
    if (isset($error)) {
      echo '<script>alert("' . $error . '");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="passwordReset.css?v=<?php echo time(); ?>">

    <style>

    </style>
</head>

<body>
    <div class="cont-pw">
        <h2>Reset Account Password.</h2>
        <p>Please enter a new password.</p>
        <form action="#" method="post">
            <!-- Password input field with show/hide feature -->
            <div class="password-input">
                <input type="password" id="password1" name="password1" placeholder="New password" required>
                <span class="show-password" onclick="togglePasswordVisibility('password1')"></span>
            </div>
            <!-- Confirm password input field with show/hide feature -->
            <div class="password-input">
                <input type="password" id="password2" name="password2" placeholder="Confirm password" required>
                <span class="show-password" onclick="togglePasswordVisibility('password2')"></span>
            </div>
            <button type="submit">CONFIRM</button>
        </form>
    </div>

    <script>
    function togglePasswordVisibility(fieldId) {
        var field = document.getElementById(fieldId);
        field.type = field.type === "password" ? "text" : "password";
        field.nextElementSibling.classList.toggle('showing');
    }
    </script>
</body>

</html>
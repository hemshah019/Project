<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../partials/dbConnect.php';
require_once '../alertService.php';


function sanitizeInput($data)
{
    return htmlspecialchars(trim($data));
}

$showError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['userName']) || empty($_POST['password'])) {
        $showError = "Username/email or password is missing.";
    } else {
        $username = sanitizeInput($_POST['userName']);
        $password = md5(sanitizeInput($_POST['password']));
        $usertype = $_POST['usertype'];
        $query = '';

        if ($usertype == 'customer') {
            $query = "SELECT CUSTOMER_ID, USERNAME, PASSWORD FROM Customer WHERE USERNAME = :username AND PASSWORD = :password";
        } elseif ($usertype == 'trader') {
            $query = "SELECT TRADER_ID, ISVERIFIED, USERNAME, PASSWORD FROM Trader WHERE USERNAME = :username AND PASSWORD = :password";
        } elseif ($usertype == 'admin') {
            $query = "SELECT ADMIN_ID, USERNAME, PASSWORD FROM Admin WHERE USERNAME = :username AND PASSWORD = :password";
        }

        try {
            $db = new Database();

            $statement = $db->executeQuery($query, [
                'username' => $username,
                'password' => $password,
            ]);

            if ($row = $db->fetchRow($statement)) {
                $_SESSION['isAuthenticated'] = true;
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $row['USERNAME'];
                // Assigning the correct user_id based on the user type
                if ($usertype == 'customer') {
                    $_SESSION['user_id'] = $row['CUSTOMER_ID'];
                    $_SESSION['user_type'] = $usertype;
                    AlertService::setSuccess('Welcome ' . $row['USERNAME'] . '!');
                    header("Location: ../cartUtils.php");
                } elseif ($usertype == 'trader') {
                    if ($row['ISVERIFIED'] == 'no') {
                        unset($_SESSION['isAuthenticated']);
                        unset($_SESSION['loggedin']);
                        unset( $_SESSION['username']);
                        AlertService::setWarning('You are not verified yet by the admin!');
                        header("Location: login.php");
                        exit();
                    }
                    $_SESSION['user_id'] = $row['TRADER_ID'];
                    $_SESSION['user_type'] = $usertype;
                    AlertService::setSuccess('Welcome ' . $row['USERNAME'] . '!');
                    header("Location: ../TraderInterface/traderInterface.php");
                    exit();
                } elseif ($usertype == 'admin') {
                    $_SESSION['user_id'] = $row['ADMIN_ID'];
                    $_SESSION['user_type'] = $usertype;
                    AlertService::setSuccess('Welcome ' . $row['USERNAME'] . '!');
                    header("Location: ../AdminInterface/adminInterface.php");
                    exit();
                }


                exit;
            } else {
                $showError = "Invalid username or password. Please try again.";
            }

            $db->closeConnection();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<?php if ($showError) { ?>
    <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px;">
        <strong>Error!</strong> <?php echo $showError; ?>
        <button type="button" style="background: none; border: none; color: #721c24; cursor: pointer; float: right;"
                onclick="this.parentNode.style.display = 'none';" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <?php AlertService::includeCSS(22); ?>
</head>
<body>
<?php AlertService::displayAlerts(); ?>
<div class="form_container">
    <form action="" method="POST">
        <div class="login">
            <div class="image_container">
                <img src="image.jpg" alt="Company Image" class="company_image">
            </div>
            <div class="form_content">
                <header>
                    <img src="Website Logo.png" alt="" class="logo">
                    <h1>Login</h1>
                    <!-- Username or Email -->
                    <label for="userName">Username:</label>
                    <input type="text" id="userName" name="userName" required
                           placeholder="Enter your username">

                    <!-- Password -->
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">

                    <!-- Login As -->
                    <div class="input_group">
                        <label for="usertype">Login As:</label>
                        <select name="usertype" id="usertype">
                            <option value="customer" selected>Customer</option>
                            <option value="admin">Admin</option>
                            <option value="trader">Trader</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <input type="submit" name="login" value="Login" class="form_btn">

                    <!-- Forgot Password Link -->
                    <a href="../VerificationPage/emailVerification.php">Forgot Password?</a>

                    <!-- Sign Up Link -->
                    <p>Don't have an account? <a href="customerSignup.php">Sign Up now</a></p>
                </header>
            </div>
        </div>
    </form>
</div>
</body>
</html>
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trader Sign Up</title>
    <link rel="stylesheet" href="traderSignup.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert-container"><div class="alert">' . $_SESSION['error'] . '</div></div>';
        unset($_SESSION['error']);
    }
    $form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
    ?>

    <div class="form_container">
        <form action="traderAuthentication.php" method="POST">
            <div class="signup">
                <div class="image_container">
                    <img src="image.jpg" alt="Company Image" class="company_image">
                </div>
                <div class="form_content">
                    <header>
                        <h1>Trader Sign Up</h1>
                    </header>
                    <div class="input-group">
                        <input type="text" id="firstName" name="firstName" required placeholder="First Name"
                            value="<?php echo isset($form_data['firstName']) ? $form_data['firstName'] : ''; ?>">
                        <input type="text" id="lastName" name="lastName" required placeholder="Last Name"
                            value="<?php echo isset($form_data['lastName']) ? $form_data['lastName'] : ''; ?>">
                    </div>
                    <input type="text" id="userName" name="userName" required placeholder="Username"
                        value="<?php echo isset($form_data['userName']) ? $form_data['userName'] : ''; ?>">
                    <input type="email" id="email" name="email" required placeholder="Email"
                        value="<?php echo isset($form_data['email']) ? $form_data['email'] : ''; ?>">
                    <input type="tel" id="phoneNumber" name="phoneNumber" required placeholder="Phone Number"
                        value="<?php echo isset($form_data['phoneNumber']) ? $form_data['phoneNumber'] : ''; ?>">
                    <input type="text" id="address" name="address" required placeholder="Address"
                        value="<?php echo isset($form_data['address']) ? $form_data['address'] : ''; ?>">
                    <div class="input-group">
                        <input type="number" id="age" name="age" required placeholder="Age"
                            value="<?php echo isset($form_data['age']) ? $form_data['age'] : ''; ?>">
                        <select name="gender">
                            <option value="M"
                                <?php echo (isset($form_data['gender']) && $form_data['gender'] == 'M') ? 'selected' : ''; ?>>
                                Male
                            </option>
                            <option value="F"
                                <?php echo (isset($form_data['gender']) && $form_data['gender'] == 'F') ? 'selected' : ''; ?>>
                                Female
                            </option>
                            <option value="O"
                                <?php echo (isset($form_data['gender']) && $form_data['gender'] == 'O') ? 'selected' : ''; ?>>
                                Other
                            </option>
                        </select>

                    </div>
                    <input type="password" id="password" name="password" required placeholder="Password">
                    <input type="password" id="confirmPassword" name="confirmPassword" required
                        placeholder="Confirm Password">
                    <div class="checkbox-container">
                        <input type="checkbox" id="terms" name="terms" value="Bike">
                        <label for="terms">I Accept the <a href="#">terms and conditions.</a></label>
                    </div>
                    <input type="submit" name="submit" value="Sign Up" class="form_btn">
                    <p>Already have an account? <a href="login.php">Login now</a></p>
                    <p>Become a Customer? <a href="customerSignup.php">SignUp now</a></p>
                </div>
            </div>
        </form>
    </div>

</body>

</html>
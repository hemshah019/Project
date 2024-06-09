<?php
session_start();
require_once('../../partials/dbConnect.php');
$customerId = $_SESSION['user_id'];
$db = new Database();
$customer = $db->getCustomerById($customerId);
$profileUpdateAlert = FALSE;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    $base64ImageData = base64_encode(file_get_contents($_FILES["profile_image"]["tmp_name"]));
    $decodedImageData = base64_decode($base64ImageData);
    $result = $db->insertProfileImage($customerId, $decodedImageData);
    if ($result) {
        $profileUpdateAlert = 'Profile image successfully updated!';
        echo '<meta http-equiv="refresh" content="3">';
    } else {
        echo "Failed to upload profile image.";
    }
}
$db->closeConnection();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Side Navigation Bar</title>
    <link rel="stylesheet" href="customerProfile.css">
    <link rel="stylesheet" href="../HeaderPage/head.css">
    <link rel="stylesheet" href="../FooterPage/footer.css">
    <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div><?php include('../HeaderPage/head.php'); ?></div>
    <div class="wrapper">
        <div class="sidebar">
            <ul>
                <li><a href="customerProfile.php"><i class="fas fa-user"></i>My Profile</a></li>
                <li><a href="myOrder.php"><i class="fas fa-cart-shopping"></i>My Orders</a></li>
                <li><a href="myWishlist.php"><i class="fas fa-heart"></i>My Whislists</a></li>
                <li><a href="myReview.php"><i class="fas fa-money-bill"></i></i>My Reviews</a></li>
                <li><a href="myCart.php"><i class="fas fa-cart-shopping"></i>My Cart</a></li>
            </ul>
        </div>
        <div class="main_content">
            <div class="he">My Profile</div>
            <div class="profile-content">
                <div class="profile-image">
                    <?php
                    $imageBase64 = $db->getProfileImage($customerId);
                    if ($imageBase64) {
                        echo '<img src="data:image/jpeg;base64,' . $imageBase64 . '" alt="User Image" style="width: 250px; height: 320px;">';
                    } else {
                        echo '<img src="../Image/usericon.png" alt="User Image" style="max-width: 200px; max-height: 200px;">';
                    }

                    ?>
                    <form id="image-upload-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                        method="post" enctype="multipart/form-data">
                        <input type="file" name="profile_image" accept="image/*" style="display: none;"
                            id="profile-image-input" onchange="document.getElementById('image-upload-form').submit()">
                        <label for="profile-image-input" class="but">Upload Picture</label>
                    </form>
                </div>
                <div class="profile-container">
                    <form id="registration-form" action="updateProfile.php" method="post">
                        <h2>Personal Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First Name:</label>
                                <input type="text" id="first-name" name="first-name"
                                    value="<?php echo htmlspecialchars($customer['FIRST_NAME']); ?>" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name:</label>
                                <input type="text" id="last-name" name="last-name"
                                    value="<?php echo htmlspecialchars($customer['LAST_NAME']); ?>" required readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username"
                                    value="<?php echo htmlspecialchars($customer['USERNAME']); ?>" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email"
                                    value="<?php echo htmlspecialchars($customer['EMAIL']); ?>" required readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address"
                                    value="<?php echo htmlspecialchars($customer['ADDRESS']); ?>" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="phone-number">Phone Number:</label>
                                <input type="tel" id="phone-number" name="phone-number"
                                    value="<?php echo htmlspecialchars($customer['PHONE']); ?>" required readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age"
                                    value="<?php echo htmlspecialchars($customer['AGE']); ?>" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select name="gender" id="gender" disabled>
                                    <option value="M" <?php echo ($customer['GENDER'] == 'male') ? 'selected' : ''; ?>>
                                        Male</option>
                                    <option value="F"
                                        <?php echo ($customer['GENDER'] == 'female') ? 'selected' : ''; ?>>Female
                                    </option>
                                    <option value="O" <?php echo ($customer['GENDER'] == 'other') ? 'selected' : ''; ?>>
                                        Other</option>
                                </select>
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="password">Password:</label>
                                <div class="password-wrapper">
                                    <input type="password" id="password" name="password"
                                        value="<?php echo htmlspecialchars($customer['PASSWORD']); ?>" required
                                        readonly>
                                    <i class="fas fa-eye" id="toggle-password"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <input type="hidden" name="update-profile" value="my_value">
                            <button type="button" class="button-group" id="edit-button">Edit</button>
                            <button type="submit" class="button-group" id="save-button" disabled>Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include('../FooterPage/footer.php'); ?>
    <script src="../HeaderPage/head.js"></script>
    <script>
    document.getElementById('edit-button').addEventListener('click', function() {
        var form = document.getElementById('registration-form');
        var inputs = form.querySelectorAll('input');
        var selects = form.querySelectorAll('select');

        inputs.forEach(function(input) {
            input.removeAttribute('readonly');
        });

        selects.forEach(function(select) {
            select.removeAttribute('disabled');
        });

        document.getElementById('save-button').removeAttribute('disabled');
    });

    document.getElementById('toggle-password').addEventListener('click', function() {
        var passwordInput = document.getElementById('password');
        var type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
    </script>
</body>

</html>
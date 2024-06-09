<?php
include('../alertService.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="Shop.css">
    <?php
    AlertService::includeCSS(); 
    ?>

</head>
<body>
    <?php
        AlertService::displayAlerts();
    ?>

    <div class="container-add">
        <div class="content">
            <div class="image_container">
                <img src="image.jpg" alt="" class="company_image">
            </div>
            
            <div class="signup">
                <header>
                    <h1>Shop Registration</h1>
                </header>
                <form action="ShopAuthrntication.php" method="post">
                    <div class="formh-group">
                        <input type="text1" id="shop-name" name="shop-name" required placeholder="Shop Name">
                    </div>
                    <div class="formh-group">
                        <input type="tel" id="phone-number" name="phone-number" required placeholder="Phone Number">
                    </div>
                    <div class="formh-group">
                        <input type="email" id="shop-email" name="shop-email" required placeholder="Email">
                    </div>
                    <div class="formh-group">
                        <textarea id="description" name="description" rows="11" cols="50" required placeholder="Description"></textarea>
                    </div>
                        <button type="submit" class="next">SignUp</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

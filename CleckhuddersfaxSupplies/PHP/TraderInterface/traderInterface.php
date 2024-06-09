<?php
    include '../../partials/dbConnect.php';
    include '../alertService.php';
    $db = new Database();
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['username'])) {
        header("Location: ../Login_Signup/login.php");
        exit;
    }
    $trader = $_SESSION['username'];


    $conn = $db->getConnection();
    $shop_id = $db->getShopIdByTraderUsername($trader);
    $_SESSION['shop_id'] = $shop_id;

    // For diffrent Trader logged in.
 
    $query = "SELECT * FROM Trader WHERE USERNAME = '$trader' ";
    $statement = oci_parse($conn, $query);
    oci_execute($statement);
    $row_trader = oci_fetch_assoc($statement);
    $traderId = $row_trader['TRADER_ID'];

    // To show shop data when Trader is logged in.
    $query1 = "SELECT * FROM SHOP WHERE TRADER_ID = '$traderId'";
    $statement_Shop = oci_parse($conn, $query1);
    oci_execute($statement_Shop);
    $fetch = oci_fetch_assoc($statement_Shop);

    // To show Trader Profile when Trader is logged in.
    $query3 = "SELECT * FROM TRADER WHERE TRADER_ID = '$traderId'";
    $statement_Trader = oci_parse($conn, $query3);
    oci_execute($statement_Trader);
    $fetchTrader = oci_fetch_assoc($statement_Trader);

    if (isset($_POST['save'])) {
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $age = $_POST['age'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $gender = $_POST['gender'];
        $username = $_POST['username'];
        $password = $_POST['password'];
    
        $query4 = "UPDATE TRADER SET FIRST_NAME = '$firstName', LAST_NAME = '$lastName', AGE = '$age', ADDRESS = '$address', EMAIL = '$email', PHONE = '$phone', GENDER = '$gender', USERNAME = '$username', PASSWORD = '$password', REGISTRATION_DATE = SYSDATE
        WHERE Trader_ID = '$traderId'";

        $statement_Trader = oci_parse($conn, $query4);
        $result1 = oci_execute($statement_Trader);

        if ($result1) {
            oci_commit($conn);
            header("Location: traderInterface.php");
            exit();
        } else {
            echo "Error updating profile!";
        }
        oci_close($conn);
    };

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trader Interface</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="traderInterface.css">
    <?php AlertService::includeCSS(); ?>
    <style>
    .formh-group2 label.but {
        display: inline-block;
        padding: 8px 12px;
        /* Adjust padding as needed */
        border: 1px solid #ccc;
        cursor: pointer;
    }

    .formh-group2 label.but:hover {
        background-color: #f0f0f0;
    }
    </style>

    <script>
    // Define the updateLabel function in the global scope
    function updateLabel(input, labelId) {
        var label = document.getElementById(labelId);
        if (input.files && input.files[0]) {
            label.innerText = input.files[0].name; // Change label text to the selected file name
        } else {
            label.innerText = "Choose Image"; // Reset label text if no file selected
        }
    }

    function updateLabel2(input, labelId) {
        var label = document.getElementById(labelId);
        if (input.files && input.files[0]) {
            label.innerText = input.files[0].name; // Change label text to the selected file name
        } else {
            label.innerText = "Choose Image"; // Reset label text if no file selected
        }
    }
    </script>


</head>

<body>
    <?php
        AlertService::displayAlerts();
    ?>

    <!-- Dashboard Header -->
    <header class="admin-header">
        <div class="header-container">
            <div class="logo">
                <img src="../Image/WebsiteLogo.png" alt="Website Logo">
            </div>
            <div class="user-info">
                <i class="fas fa-user"></i>
                <div class="user-name"><?php echo htmlspecialchars($trader); ?></div>
                <div class="logout">
                    <a href="../Login_Signup/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <div class="main-container">
        <!-- Side bar for Trader Panel -->
        <aside class="sidebar">
            <h1>TRADER PANEL</h1>
            <button id="dashboard-button">Dashboard</button>
            <ul>
                <li><a href="#" onclick="showContent('traderprofile')">Manage Profile</a></li>
                <li><a href="#" onclick="showContent('shops')">Manage Shops</a></li>
                <li><a href="#" onclick="showContent('orders')">Manage Orders</a></li>
                <li><a href="#" onclick="showContent('products')">Manage Products</a></li>
                </li>
            </ul>
        </aside>

        <!-- Default Content showing all Dashbord boxes -->
        <section class="admin-interface" id="admin-interface">
            <div class="container" id="content-container">
                <div class="boxx" style="background-image: url('../Image/customer.jpg');"
                    onclick="showContent('traderprofile')">
                    <h3>Profile</h3>
                </div>
                <div class="boxx" style="background-image: url('../Image/customer.jpg');"
                    onclick="showContent('shops')">
                    <h3>Shops</h3>
                </div>
                <div class="boxx" style="background-image: url('../Image/trader.jpg');" onclick="showContent('orders')">
                    <h3>Orders</h3>
                </div>
                <div class="boxx" style="background-image: url('../Image/products.jpeg');"
                    onclick="showContent('products')">
                    <h3>Products</h3>
                </div>
                <div class="boxx" style="background-image: url('../Image/report.jpg');"
                    onclick="window.open('http://127.0.0.1:8080/apex/f?p=101:LOGIN_DESKTOP:6178725944709:::::', '_blank')">
                    <h3>Reports</h3>
                </div>
                <div class="boxx" style="background-image: url('../Image/Dashboard.jpeg');"
                    onclick="window.open('http://127.0.0.1:8080/apex/f?p=101:LOGIN_DESKTOP:6178725944709:::::', '_blank')">
                    <h3>Oracle<br>Dashboard</h3>
                </div>
            </div>

            <!-- All trader Shops Details -->
            <section class="shop-details" id="shop-details">
                <h2 class="details-heading">Shop Details</h2>
                <table class="shop-table">
                    <thead>
                        <tr>
                            <th>Shop Id</th>
                            <th>Shop Image</th>
                            <th>Shop Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = "SELECT Shop_Id, Shop_image, Shop_Name, Description, Status
                            FROM SHOP WHERE Trader_Id = '$traderId'";
                            
                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            $sn = 1;
                            while ($row = oci_fetch_assoc($stid)) {
                                $shopImageBase64 = $db->getShopImage($row['SHOP_ID']);

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($sn++) . "</td>";
                                echo "<td>";
                                if ($shopImageBase64) {
                                    echo '<img src="data:image/jpeg;base64,' . $shopImageBase64 . '" alt="' . htmlspecialchars($row['SHOP_NAME']) . '" style="width: 100%; height: 130px;">';
                                } else {
                                    echo '<img src="../Image/path_to_placeholder_image.jpg" alt="' . htmlspecialchars($row['SHOP_NAME']) . ' Image" style="width: 100%; height: auto;">';
                                }
                                echo "</td>";
                                echo "<td>" . htmlspecialchars($row['SHOP_NAME']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['DESCRIPTION']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['STATUS']) . "</td>";
                                echo 
                                    '<td>
                                        <button class="update-btn" onclick="">Update</button>
                                        <button class="delete-btn" onclick="deleteShop(' . $row['SHOP_ID'] . ')">Delete</button>
                                    </td>';
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </section>

            <!-- All trader Orders Details -->
            <section class="order-details" id="order-details">
                <h2 class="details-heading">Order Details</h2>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Order Id</th>
                            <th>Customer Name</th>
                            <th>Product Name</th>
                            <th>Order_Date</th>
                            <th>Status</th>
                            <th>Slot Day</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $query = "SELECT ORDERS.Order_Id, CUSTOMER.First_Name || ' ' || CUSTOMER.Last_Name AS Customer_Name, PRODUCT.Product_Name, TO_CHAR(ORDERS.Order_Date, 'YYYY-MM-DD HH24:MM:SS') AS Order_Date, ORDERS.Order_Status As Status, 
                            ORDERS.COLLECTION_DATE, ORDERS.COLLECTION_SLOT
                            FROM ORDERS
                            JOIN CUSTOMER ON ORDERS.Customer_Id = CUSTOMER.Customer_Id
                            JOIN ORDER_PRODUCT ON ORDERS.Order_Id = ORDER_PRODUCT.Order_Id
                            JOIN PRODUCT ON ORDER_PRODUCT.Product_Id = PRODUCT.Product_Id
                            JOIN SHOP ON PRODUCT.Shop_Id = SHOP.Shop_Id
                            WHERE SHOP.Trader_Id = '$traderId'";
                        
                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            $sn = 1;
                            while ($row = oci_fetch_assoc($stid)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($sn++) . "</td>";
                                echo "<td>" . htmlspecialchars($row['ORDER_ID']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['CUSTOMER_NAME']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['PRODUCT_NAME']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['ORDER_DATE']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['STATUS']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['COLLECTION_DATE']) .' '. htmlspecialchars($row['COLLECTION_SLOT']) . "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </section>

            <!-- All trader Product Details -->
            <section class="product-details" id="product-details">
                <h2 class="details-heading">Product Details</h2>
                <div class="btun">
                    <button id="add-product-btn">Add Product</button>
                </div>
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Shop Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = "SELECT PRODUCT.Product_Id, PRODUCT.Product_Image, PRODUCT.Product_Name, PRODUCT.Description, PRODUCT.Price, PRODUCT.quantity_per_item, PRODUCT.Stock, PRODUCT.min_order, PRODUCT.max_order,
                            PRODUCT.allergy_info, SHOP.Shop_Name
                            FROM PRODUCT
                            JOIN SHOP ON PRODUCT.Shop_Id = SHOP.Shop_Id 
                            WHERE SHOP.Trader_Id = '$traderId'";
                        
                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            $sn = 1;
                            while ($row = oci_fetch_assoc($stid)) {
                                $imageBase64 = $db->getProductImage($row['PRODUCT_ID']);
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($sn++) . "</td>";
                                echo "<td>";
                                if ($imageBase64) {
                                    echo '<img src="data:image/jpeg;base64,' . $imageBase64 . '" alt="' . htmlspecialchars($row['PRODUCT_NAME']) . '" style="width: 100%; height: 130px;">';
                                } else {
                                    echo '<img src="../Image/path_to_placeholder_image.jpg" alt="' . htmlspecialchars($row['PRODUCT_NAME']) . ' Image" style="width: 100%; height: auto;">';
                                }
                                echo "</td>";
                                echo "<td>" . htmlspecialchars($row['PRODUCT_NAME']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['DESCRIPTION']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['PRICE']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['STOCK']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['SHOP_NAME']) . "</td>";

                                echo "<td>
                                    <button class='upd-btn' 
                                        onclick=\"openUpdateModal(
                                            '{$row['PRODUCT_ID']}', 
                                            '" . addslashes($row['PRODUCT_NAME']) . "', 
                                            '" . addslashes($row['DESCRIPTION']) . "', 
                                            {$row['PRICE']}, 
                                            {$row['STOCK']}, 
                                            '" . addslashes($row['ALLERGY_INFO']) . "',
                                            {$row['QUANTITY_PER_ITEM']},
                                            {$row['MIN_ORDER']},
                                            {$row['MAX_ORDER']},
                                        )\">
                                        Update
                                    </button>
                                    <button class='del-btn' onclick=\"deleteProduct({$row['PRODUCT_ID']})\">Delete</button>
                                </td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </section>

            <!-- Trader Profile-->
            <section class="trader-profile" id="trader-profile">
                <div class="containers">
                    <h2>Trader Profile</h2>
                    <form action="" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name"
                                    value="<?php echo htmlspecialchars($fetchTrader['FIRST_NAME'], ENT_QUOTES); ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name"
                                    value="<?php echo htmlspecialchars($fetchTrader['LAST_NAME'], ENT_QUOTES); ?>"
                                    required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="number" id="age" name="age"
                                    value="<?php echo htmlspecialchars($fetchTrader['AGE'], ENT_QUOTES); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone"
                                    value="<?php echo htmlspecialchars($fetchTrader['PHONE'], ENT_QUOTES); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address"
                                value="<?php echo htmlspecialchars($fetchTrader['ADDRESS'], ENT_QUOTES); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                value="<?php echo htmlspecialchars($fetchTrader['EMAIL'], ENT_QUOTES); ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <input type="text" id="gender" name="gender"
                                    value="<?php echo htmlspecialchars($fetchTrader['GENDER'], ENT_QUOTES); ?>"
                                    required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username"
                                    value="<?php echo htmlspecialchars($fetchTrader['USERNAME'], ENT_QUOTES); ?>"
                                    required>
                            </div>
                            <div class="form-group password-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password"
                                    value="<?php echo htmlspecialchars($fetchTrader['PASSWORD'], ENT_QUOTES); ?>"
                                    required>
                                <span id="togglePassword" class="toggle-password">👁️</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="button-container">
                                <button type="button" id="editButton" class="editbtn">Edit</button>
                            </div>
                            <div class="button-container">
                                <button type="submit" id="saveButton" name="save" class="savebtn"
                                    style="display: none;">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </section>
    </div>

    <!-- Update Shop Modal -->
    <div id="updateShopModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h1>Update Shop</h1>
            <form action="traderAction.php" method="post" enctype="multipart/form-data">
                <div class="formh-group">
                    <label for="shop-name">Shop Name:</label>
                    <input type="text" id="shop-name" name="shop-name"
                        value="<?php echo htmlspecialchars($fetch['SHOP_NAME'], ENT_QUOTES); ?>" required>
                </div>
                <div class="formh-group">
                    <label for="shop-email">Shop Email:</label>
                    <input type="email" id="shop-email" name="shop-email"
                        value="<?php echo htmlspecialchars($fetch['SHOP_EMAIL'], ENT_QUOTES); ?>" required>
                </div>
                <div class="formh-group2">
                    <label for="shop-image" class="but">Product Image:</label>
                    <input type="file" id="shop-image" name="shop-image" accept="image/*" style="display: none;" onchange="updateLabel(this, 'file-select-label4')">
                    <span id="file-select-label4">Choose Image</span> <!-- Add a span for displaying the selected file name -->
                </div>
                <div class="formh-group">
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description"
                        value="<?php echo htmlspecialchars($fetch['DESCRIPTION'], ENT_QUOTES); ?>" required>
                </div>
                <input type="hidden" name="updateShop" value="">
                <button type="submit" class="next" name="submit">Update</button>
            </form>
        </div>
    </div>

    <!-- Update Product Modal -->
    <div id="updateProductModal" class="modal1">
        <div class="modal-content1">
            <span class="close-update">&times;</span>
            <h1>Update Product</h1>
            <form action="traderAction.php" method="post" enctype="multipart/form-data">
                <div class="formh-group1">
                    <label for="update-product-name">Product Name:</label>
                    <input type="text" id="update-product-name" name="product-name" required>
                </div>
                <div class="formh-group1 inline">
                    <label for="update-price">Price:</label>
                    <input type="number" id="update-price" name="price" required>
                </div>
                <div class="formh-group1 inline">
                    <label for="update-stock">Stock:</label>
                    <input type="number" id="update-stock" name="stock" required>
                </div>
                <div class="formh-group1 inline">
                    <label for="update-quantity">Quantity Per Item:</label>
                    <input type="number" id="update-quantity-per-item" name="quantity">
                </div>
                <div class="formh-group1 inline">
                    <label for="update-min-order">Min Order:</label>
                    <input type="number" id="update-min-order" name="min-order">
                </div>
                <div class="formh-group1 inline">
                    <label for="update-max-order">Max Order:</label>
                    <input type="number" id="update-max-order" name="max-order">
                </div>
                <div class="formh-group2">
                    <label for="product-image3" class="but">Product Image:</label>
                    <input type="file" id="product-image3" name="product-image" accept="image/*"
                        style="display: none;" onchange="updateLabel2(this, 'file-select-label3')">
                    <span id="file-select-label3">Choose Image</span>
                </div>
                <div class="formh-group1">
                    <label for="update-allergy-info">Allergy Info:</label>
                    <input type="text" id="update-allergy-info" name="allergy-info">
                </div>
                <div class="formh-group1">
                    <label for="category">Product Category</label>
                    <select type="category" id="category" name="category" required>
                        <option value="">Select a category</option>
                        <?php
                        // Fetch categories
                        $query_categories = "SELECT * FROM CATEGORY";
                        $statement_categories = oci_parse($conn, $query_categories);
                        oci_execute($statement_categories);
                        while ($row_category = oci_fetch_assoc($statement_categories)) {
                            echo '<option value="' . $row_category['CATEGORY_ID'] . '">' . $row_category['CATEGORY_NAME'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="formh-group1">
                    <label for="update-description">Description:</label>
                    <input type="text" id="update-description" name="description" required>
                </div>
                <input type="hidden" name="updateproduct" value="">
                <input type="hidden" id="update_productId" name="update_productId" value="">
                <button type="submit" class="next1" name="submits">Update</button>
            </form>
        </div>
    </div>


    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal2">
        <div class="modal-content2">
            <span class="close-add">&times;</span>
            <h1>Add Product</h1>
            <form action="traderAction.php" method="post" enctype="multipart/form-data">
                <div class="formh-group2">
                    <label for="product-name">Product Name:</label>
                    <input type="text" id="product-name" name="product-name" required>
                </div>
                <div class="formh-group2 inline">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" required>
                </div>
                <div class="formh-group2 inline">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" required>
                </div>
                <div class="formh-group2 inline">
                    <label for="min-order">Min Order:</label>
                    <input type="number" id="min-order" name="min-order" required>
                </div>
                <div class="formh-group2 inline">
                    <label for="max-order">Max Order:</label>
                    <input type="number" id="max-order" name="max-order" required>
                </div>
                <div class="formh-group2">
                    <label for="quantity-per-item">Quantity Per Item:</label>
                    <input type="number" id="quantity-per-item" name="quantity-per-item" required>
                </div>
                <div class="formh-group2">
                    <label for="product-image2" class="but">Product Image:</label>
                    <input type="file" id="product-image2" name="product-image" accept="image/*" style="display: none;"
                        onchange="updateLabel(this, 'file-select-label2')">
                    <span id="file-select-label2">Choose Image</span>
                </div>
                <div class="formh-group2">
                    <label for="allergy-info">Allergy Info:</label>
                    <input type="text" id="allergy-info" name="allergy-info" required>
                </div>
                <div class="formh-group2">
                    <label for="category">Product Category</label>
                    <select type="category" id="category" name="category" required>
                        <option value="">Select a category</option>
                        <?php
                        // Fetch categories
                        $query_categories = "SELECT * FROM CATEGORY";
                        $statement_categories = oci_parse($conn, $query_categories);
                        oci_execute($statement_categories);
                        while ($row_category = oci_fetch_assoc($statement_categories)) {
                            echo '<option value="' . $row_category['CATEGORY_ID'] . '">' . $row_category['CATEGORY_NAME'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="formh-group2">
                    <label for="description">Description:</label>
                    <input type="text" id="Description" name="description" required>
                </div>
                <input type="hidden" name="addproduct" value="">
                <button type="submit" class="next2" name="addproduct">Add</button>
            </form>
        </div>
    </div>


    <script src="traderInterface.js"></script>
    <script>
        // Navigate to Apex Oracle Application
        function navigateTo(url) {
            window.location.href = url;
        }

        // Edit and Save Button
        document.getElementById('editButton').addEventListener('click', function() {
            var inputFields = document.querySelectorAll('input:not([type="submit"])');
            inputFields.forEach(function(input) {
                input.removeAttribute('readonly');
            });
            document.getElementById('editButton').style.display = 'none';
            document.getElementById('saveButton').style.display = 'block';
        });

        // Password Hide and Show
        document.getElementById('togglePassword').addEventListener('click', function() {
            var passwordField = document.getElementById('password');
            var type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        function deleteEntity(entityType, entityId) {
            if (confirm("Are you sure you want to delete this " + entityType + "?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "deleteTraderRecord.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload();
                    }
                };
                xhr.send("type=" + entityType + "&id=" + entityId);
            }
        }

        function deleteProduct(productId) {
            deleteEntity('product', productId);
        }

        function deleteShop(shopId) {
            deleteEntity('shop', shopId);
        }


        function openUpdateModal(productId, productName, description, price, stock, allergyInfo, qtyPerItem, minOrder,
            maxOrder) {

            document.getElementById('update-product-name').value = productName;
            document.getElementById('update-description').value = description;
            document.getElementById('update-price').value = price;
            document.getElementById('update-stock').value = stock;
            document.getElementById('update-allergy-info').value = allergyInfo || '';
            document.getElementById('update-quantity-per-item').value = qtyPerItem || '';
            document.getElementById('update-min-order').value = minOrder || '';
            document.getElementById('update-max-order').value = maxOrder || '';
            document.getElementById('update_productId').value = productId;
            document.getElementById('update-category').selectedIndex = 0;

            // Display the modal
            document.getElementById('updateProductModal').style.display = 'block';
        }

        document.querySelector('.close-update').onclick = function() {
            document.getElementById('updateProductModal').style.display = 'none';
        };
        window.onclick = function(event) {
            if (event.target == document.getElementById('updateProductModal')) {
                document.getElementById('updateProductModal').style.display = 'none';
            }
        };
    </script>








</body>

</html>
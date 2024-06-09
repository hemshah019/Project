<?php
include '../../partials/dbConnect.php';
include '../alertService.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../Login_Signup/login.php");
    exit;
}

$db = new Database();

$conn = $db->getConnection();
if (!$conn) {
    $m = oci_error();
    $_SESSION['error'] = $m['message'];
    exit();
} else {
    $_SESSION['notification'] = "Connected to Oracle!";
}

$admin = $_SESSION['username'];

if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $traderId = $_GET['id'];

    // Prepare the update query
    $updateQuery = "UPDATE TRADER SET isVerified ='yes' WHERE Trader_Id = :traderId";
    $stmt = oci_parse($conn, $updateQuery);
    oci_bind_by_name($stmt, ":traderId", $traderId);

    // Execute the update
    if (oci_execute($stmt)) {
        oci_commit($conn);
    } else {
        oci_rollback($conn);
        $error = oci_error($stmt);
        $message = "Failed to approve trader: " . $error['message'];
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'disapprove' && isset($_GET['id'])) {
    $traderId = $_GET['id'];

    // Prepare the update query
    $updateQuery = "UPDATE TRADER SET isVerified = 'no' WHERE Trader_ID = :traderId";
    $stmt = oci_parse($conn, $updateQuery);
    oci_bind_by_name($stmt, ":traderId", $traderId);

    // Execute the update
    if (oci_execute($stmt)) {
        oci_commit($conn);
    } else {
        oci_rollback($conn);
        $error = oci_error($stmt);
        $message = "Failed to disapprove trader: " . $error['message'];
    }
}


if (isset($_GET['action']) && $_GET['action'] == 'approveShop' && isset($_GET['id'])) {
    $shopId = $_GET['id'];

    // Prepare the update query
    $updateQuery = "UPDATE SHOP SET Status = 'ACTIVE' WHERE Shop_Id = :shopId";
    $stmt = oci_parse($conn, $updateQuery);
    oci_bind_by_name($stmt, ":shopId", $shopId);

    // Execute the update
    if (oci_execute($stmt)) {
        oci_commit($conn);
    } else {
        oci_rollback($conn);
        $error = oci_error($stmt);
        $message = "Failed to approve shop: " . $error['message'];
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'disapproveShop' && isset($_GET['id'])) {
    $shopId = $_GET['id'];

    // Prepare the update query
    $updateQuery = "UPDATE SHOP SET Status = 'INACTIVE' WHERE Shop_ID = :shopId";
    $stmt = oci_parse($conn, $updateQuery);
    oci_bind_by_name($stmt, ":shopId", $shopId);

    // Execute the update
    if (oci_execute($stmt)) {
        oci_commit($conn);
    } else {
        oci_rollback($conn);
        $error = oci_error($stmt);
        $message = "Failed to disapprove shop: " . $error['message'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Interface</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="adminInterface.css">
    <?php AlertService::includeCSS(); ?>
</head>
<body>
<?php
AlertService::displayAlerts();
?>
<header class="admin-header">
    <div class="header-container">
        <div class="logo">
            <img src="../Image/WebsiteLogo.png" alt="Website Logo">
        </div>
        <div class="user-info">
            <i class="fas fa-user"></i>
            <div class="user-name"><?php echo htmlspecialchars($admin); ?></div>
            <div class="logout">
                <a href="../Login_Signup/logout.php">Logout</a>
            </div>
        </div>
    </div>
</header>
<div class="main-container">
    <aside class="sidebar">
        <h1>ADMIN PANEL</h1>
        <button id="dashboard-button">Dashboard</button>
        <ul>
            <li><a href="#" onclick="showContent('customer')">Manage Customer</a></li>
            <li><a href="#" onclick="showContent('traders')">Manage Traders</a></li>
            <li><a href="#" onclick="showContent('products')">Manage Products</a></li>
            <li><a href="#" onclick="showContent('request')">Trader Request</a></li>
            <li><a href="#" onclick="showContent('Shopreuest')">Shop Request</a></li>
        </ul>
    </aside>
    <section class="admin-interface" id="admin-interface">
        <div class="container" id="content-container">
            <!-- Default Content showing all boxes -->
            <div class="boxx" style="background-image: url('../Image/customer.jpg');" onclick="showContent('customer')">
                <h3>Customer</h3>
            </div>
            <div class="boxx" style="background-image: url('../Image/trader.jpg');" onclick="showContent('traders')">
                <h3>Traders</h3>
            </div>
            <div class="boxx" style="background-image: url('../Image/products.jpeg');"
                 onclick="showContent('products')">
                <h3>Products</h3>
            </div>
            <div class="boxx" style="background-image: url('../Image/order.jpg');" onclick="showContent('request')">
                <h3>Requests</h3>
            </div>
            <div class="boxx" style="background-image: url('../Image/report.jpg');"
                 onclick="window.open('http://127.0.0.1:8080/apex/f?p=100:LOGIN_DESKTOP:6267694608686:::::', '_blank')">
                <h3>Reports</h3>
            </div>
            <div class="boxx" style="background-image: url('../Image/Dashboard.jpeg');"
                 onclick="window.open('http://127.0.0.1:8080/apex/f?p=100:LOGIN_DESKTOP:6267694608686:::::', '_blank')">
                <h3>Oracle<br>Dashboard</h3>
            </div>
        </div>
        <section class="customer-details" id="customer-details">
            <h2 class="details-heading">Customer Details</h2>
            <table class="customer-table">
                <thead>
                <tr>
                    <th>S.N</th>
                    <th>Customer Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT CUSTOMER.Customer_Id, CUSTOMER.First_Name || ' ' || CUSTOMER.Last_Name AS Customer_Name, CUSTOMER.Username, CUSTOMER.Email, CUSTOMER.Phone
                            FROM CUSTOMER
                            ORDER BY CUSTOMER.Customer_Id ASC";

                $stid = oci_parse($conn, $query);
                oci_execute($stid);

                $sn = 1;
                while ($row = oci_fetch_assoc($stid)) {
                    echo "<tr>";
                    echo "<td>" . $sn++ . "</td>";
                    echo "<td>" . $row['CUSTOMER_NAME'] . "</td>";
                    echo "<td>" . $row['USERNAME'] . "</td>";
                    echo "<td>" . $row['EMAIL'] . "</td>";
                    echo "<td>" . $row['PHONE'] . "</td>";
                    echo '<td><button onclick="deleteCustomer(' . $row['CUSTOMER_ID'] . ')">Delete</button></td>';
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </section>
        <section class="trader-details" id="trader-details">
            <h2 class="details-heading">Trader Details</h2>
            <table class="trader-table">
                <thead>
                <tr>
                    <th>Trader Id</th>
                    <th>Trader Name</th>
                    <th>Shop Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT TRADER.Trader_Id, TRADER.First_Name || ' ' || TRADER.Last_Name AS Trader_Name, TRADER.Username, TRADER.Email, TRADER.Phone, SHOP.Shop_Name
                            FROM TRADER
                            INNER JOIN SHOP ON TRADER.Trader_Id = SHOP.Trader_Id
                            WHERE TRADER.isVerified = 'yes'
                            ORDER BY TRADER.Trader_Id ASC";


                $stid = oci_parse($conn, $query);
                oci_execute($stid);

                $sn = 1;
                while ($row = oci_fetch_assoc($stid)) {
                    echo "<tr>";
                    echo "<td>" . $sn++ . "</td>";
                    echo "<td>" . $row['TRADER_NAME'] . "</td>";
                    echo "<td>" . $row['SHOP_NAME'] . "</td>";
                    echo "<td>" . $row['USERNAME'] . "</td>";
                    echo "<td>" . $row['EMAIL'] . "</td>";
                    echo "<td>" . $row['PHONE'] . "</td>";
                    echo '<td><button onclick="deleteTrader(' . $row['TRADER_ID'] . ')">Delete</button></td>';
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </section>
        <section class="product-details" id="product-details">
            <h2 class="details-heading">Product Details</h2>
            <table class="product-table">
                <thead>
                <tr>
                    <th>Product Id</th>
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
                $query = "SELECT PRODUCT.Product_Id,  PRODUCT.Product_Name, PRODUCT.Description, PRODUCT.Price, PRODUCT.Stock, SHOP.Shop_Name
                            FROM PRODUCT
                            INNER JOIN SHOP ON PRODUCT.Shop_Id = SHOP.Shop_Id
                            ORDER BY PRODUCT.Product_Id ASC";

                $stid = oci_parse($conn, $query);
                oci_execute($stid);

                $sn = 1;
                while ($row = oci_fetch_assoc($stid)) {
                    echo "<tr>";
                    echo "<td>" . $sn++ . "</td>";
                    $imageBase64 = $db->getProductImage($row['PRODUCT_ID']);
                    echo '<td>'; // Set a fixed height for the image container and hide overflow
                    if ($imageBase64) {
                        echo '<img src="data:image/jpeg;base64,' . $imageBase64 . '" alt="Customer Image" style="width: 200PX; height: 100PX;">';
                    } else {
                        echo '<img src="path_to_placeholder_image.jpg" alt="' . $name . ' Image" style="width: 200PX; height: 100PX;">';
                    }
                    echo '</td>';

                    echo "<td>" . $row['PRODUCT_NAME'] . "</td>";
                    echo "<td>" . $row['DESCRIPTION'] . "</td>";
                    echo "<td>" . $row['PRICE'] . "</td>";
                    echo "<td>" . $row['STOCK'] . "</td>";
                    echo "<td>" . $row['SHOP_NAME'] . "</td>";
                    echo '<td><button onclick="deleteProduct(' . $row['PRODUCT_ID'] . ')">Delete</button></td>';
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </section>
        <section class="trader-requests" id="trader-requests">
            <div class="trader-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Approved Traders</h3>
                    </div>
                    <div class="card-body scrollable">
                        <!-- Table for Approved Traders -->
                        <table class="request-table">
                            <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Shop Name</th>
                                <th>Phone no.</th>
                                <th>Location</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody>
                            <?php
                            $query = "SELECT TRADER.Trader_Id, TRADER.First_Name || ' ' || TRADER.Last_Name AS Trader_Name, TRADER.Email, TRADER.Phone, TRADER.Address, SHOP.Shop_Name
                                        FROM TRADER
                                        INNER JOIN SHOP ON TRADER.Trader_Id = SHOP.Trader_Id WHERE TRADER.isVerified = 'yes'
                                        ORDER BY TRADER.Trader_Id ASC";

                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            $sn = 1;
                            while ($row = oci_fetch_assoc($stid)) {
                                echo "<tr>";
                                echo "<td>" . $sn++ . "</td>";
                                echo "<td>" . $row['TRADER_NAME'] . "</td>";
                                echo "<td>" . $row['EMAIL'] . "</td>";
                                echo "<td>" . $row['SHOP_NAME'] . "</td>";
                                echo "<td>" . $row['PHONE'] . "</td>";
                                echo "<td>" . $row['ADDRESS'] . "</td>";
                                echo "<td> <a class='btn btn-danger' href='?action=disapprove&id=" . htmlspecialchars($row['TRADER_ID']) . "' onclick='return confirm(\"Are you sure you want to disapprove this trader?\")'>Disapprove</a></td>\n";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="trader-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Pending Traders</h3>
                    </div>
                    <div class="card-body scrollable">
                        <!-- Table for Pending Traders -->
                        <table class="request-table">
                            <!-- Table headers -->
                            <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Shop Name</th>
                                <th>Phone no.</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody>
                            <?php
                            $query = "SELECT TRADER.Trader_Id, TRADER.First_Name || ' ' || TRADER.Last_Name AS Trader_Name, TRADER.Email, TRADER.Phone, TRADER.Address, SHOP.Shop_Name
                                        FROM TRADER
                                        INNER JOIN SHOP ON TRADER.Trader_Id = SHOP.Trader_Id WHERE TRADER.isVerified = 'no'
                                        ORDER BY TRADER.Trader_Id ASC";

                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            $sn = 1;
                            while ($row = oci_fetch_assoc($stid)) {
                                echo "<tr>";
                                echo "<td>" . $sn++ . "</td>";
                                echo "<td>" . $row['TRADER_NAME'] . "</td>";
                                echo "<td>" . $row['EMAIL'] . "</td>";
                                echo "<td>" . $row['SHOP_NAME'] . "</td>";
                                echo "<td>" . $row['PHONE'] . "</td>";
                                echo "<td>" . $row['ADDRESS'] . "</td>";
                                echo "<td><a class='btn btn-success' href='?action=approve&id=" . htmlspecialchars($row['TRADER_ID']) . "' onclick='return confirm(\"Are you sure you want to approve this trader?\")'>Approve</a></td>\n";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <section class="shop-requests" id="shop-requests">
            <div class="shop-container">
                <div class="shop-card">
                    <div class="shop-card-header">
                        <h3>Approved Shops</h3>
                    </div>
                    <div class="card-body scroll">
                        <!-- Table for Approved Traders -->
                        <table class="shop-table">
                            <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Shop Image</th>
                                <th>Shop Name</th>
                                <th>Registration Date</th>
                                <th>Description</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody>
                            <?php
                            $query = "SELECT Shop_Id, Shop_image, Shop_Name, Registration_Date, Description, Shop_Email
                                        FROM SHOP WHERE UPPER(Status) = UPPER('ACTIVE')
                                        ORDER BY SHOP.Shop_Id ASC";

                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            $sn = 1;
                            while ($row = oci_fetch_assoc($stid)) {
                                $shopImageBase64 = $db->getShopImage($row['SHOP_ID']);
                                echo "<tr>";
                                echo "<td>" . $sn++ . "</td>";
                                echo "<td>";
                                if ($shopImageBase64) {
                                    echo '<img src="data:image/jpeg;base64,' . $shopImageBase64 . '" alt="' . htmlspecialchars($row['SHOP_NAME']) . '" style="width: 200px; height: 130px;">';
                                } else {
                                    echo '<img src="../Image/path_to_placeholder_image.jpg" alt="' . htmlspecialchars($row['SHOP_NAME']) . ' Image" style="width: 200px; height: auto;">';
                                }
                                echo "</td>";
                                echo "<td>" . $row['SHOP_NAME'] . "</td>";
                                echo "<td>" . $row['REGISTRATION_DATE'] . "</td>";
                                echo "<td>" . $row['DESCRIPTION'] . "</td>";
                                echo "<td>" . $row['SHOP_EMAIL'] . "</td>";
                                echo "<td> <a class='btns btn-danger-shop' href='?action=disapproveShop&id=" . htmlspecialchars($row['SHOP_ID']) . "' onclick='return confirm(\"Are you sure you want to disapprove this shop?\")'>Disapprove</a></td>\n";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="shop-container">
                <div class="shop-card">
                    <div class="shop-card-header">
                        <h3>Pending Shops</h3>
                    </div>
                    <div class="shop-card-body scroll">
                        <!-- Table for Pending Traders -->
                        <table class="shop-table">
                            <!-- Table headers -->
                            <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Shop Image</th>
                                <th>Shop Name</th>
                                <th>Registration Date</th>
                                <th>Description</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody>
                            <?php
                            $query = "SELECT Shop_Id, Shop_image, Shop_Name, Registration_Date, Description, Shop_Email
                                        FROM SHOP WHERE UPPER(Status) = UPPER('INACTIVE')
                                        ORDER BY SHOP.Shop_Id ASC";

                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            $sn = 1;
                            while ($row = oci_fetch_assoc($stid)) {
                                $shopImageBase64 = $db->getShopImage($row['SHOP_ID']);
                                echo "<tr>";
                                echo "<td>" . $sn++ . "</td>";
                                echo "<td>";
                                if ($shopImageBase64) {
                                    echo '<img src="data:image/jpeg;base64,' . $shopImageBase64 . '" alt="' . htmlspecialchars($row['SHOP_NAME']) . '" style="width: 200px; height: 130px;">';
                                } else {
                                    echo '<img src="../Image/path_to_placeholder_image.jpg" alt="' . htmlspecialchars($row['SHOP_NAME']) . ' Image" style="width: 200px%; height: auto;">';
                                }
                                echo "</td>";
                                echo "<td>" . $row['SHOP_NAME'] . "</td>";
                                echo "<td>" . $row['REGISTRATION_DATE'] . "</td>";
                                echo "<td>" . $row['DESCRIPTION'] . "</td>";
                                echo "<td>" . $row['SHOP_EMAIL'] . "</td>";
                                echo "<td><a class='btns btn-success-shop' href='?action=approveShop&id=" . htmlspecialchars($row['SHOP_ID']) . "' onclick='return confirm(\"Are you sure you want to approve this shop?\")'>Approve</a></td>\n";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </section>
</div>
<script src="adminInterface.js"></script>
<script>
    function navigateTo(url) {
        window.location.href = url;
    }
</script>
<script>
    function deleteEntity(entityType, entityId) {
        if (confirm("Are you sure you want to delete this " + entityType + "?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "deleteRecord.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    location.reload(); // Reload the page to see the changes
                }
            };
            xhr.send("type=" + entityType + "&id=" + entityId);
        }
    }

    function deleteTrader(traderId) {
        deleteEntity('trader', traderId);
    }

    function deleteProduct(productId) {
        deleteEntity('product', productId);
    }

    function deleteCustomer(customerId) {
        deleteEntity('customer', customerId);
    }
</script>

</body>
</html>

<?php
oci_free_statement($stid);
oci_close($conn);
?>

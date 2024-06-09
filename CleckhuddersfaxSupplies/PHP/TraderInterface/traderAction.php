<?php
  include '../../partials/dbConnect.php';
  $db = new Database();
  $conn = $db->getConnection();
  session_start();

    // Add product
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addproduct'])) {
        // Extract form values
        $productName = $_POST['product-name'];
        $description = $_POST['description'];
        $price = $_POST['price'] ;
        $quantityPerItem = $_POST['quantity-per-item'];
        $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
        $minOrder = isset($_POST['min-order']) ? $_POST['min-order'] : '';
        $maxOrder = isset($_POST['max-order']) ? $_POST['max-order'] : '';
        $allergyInfo = isset($_POST['allergy-info']) ? $_POST['allergy-info'] : '';
        $shop_id = $_SESSION['shop_id'];
        $category = isset($_POST['category']) ? $_POST['category'] : '';
   
        if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
            $productImage = $_FILES['product-image'];
            $base64ImageData = base64_encode(file_get_contents($productImage['tmp_name']));
            $decodedImageData = base64_decode($base64ImageData);
        
            // Insert the product into the database with an empty BLOB for the image
            $query = "INSERT INTO PRODUCT (product_image, product_name, description, price, quantity_per_item, stock, min_order, max_order, allergy_info, shop_id, category_id, discount_id) 
                      VALUES (EMPTY_BLOB(), :productName, :description, :price, :quantityPerItem, :stock, :minOrder, :maxOrder, :allergyInfo, :shop_id, :category, 4001) 
                      RETURNING product_image INTO :imageData";
            $statement = oci_parse($conn, $query);
        
            // Bind the variables
            oci_bind_by_name($statement, ":productName", $productName);
            oci_bind_by_name($statement, ":description", $description);
            oci_bind_by_name($statement, ":price", $price);
            oci_bind_by_name($statement, ":quantityPerItem", $quantityPerItem);
            oci_bind_by_name($statement, ":stock", $stock);
            oci_bind_by_name($statement, ":minOrder", $minOrder);
            oci_bind_by_name($statement, ":maxOrder", $maxOrder);
            oci_bind_by_name($statement, ":allergyInfo", $allergyInfo);
            oci_bind_by_name($statement, ":shop_id", $shop_id);
            oci_bind_by_name($statement, ":category", $category);
        
            // Create a new descriptor for the BLOB
            $lob = oci_new_descriptor($conn, OCI_D_LOB);
            oci_bind_by_name($statement, ":imageData", $lob, -1, OCI_B_BLOB);
        
            // Execute the query
            $result = oci_execute($statement, OCI_DEFAULT);
            if ($result) {
                // Save the image data to the BLOB
                if ($lob->save($decodedImageData)) {
                    oci_commit($conn);
                    echo "Product added successfully.";
                    echo '<meta http-equiv="refresh" content="3">';
                } else {
                    oci_rollback($conn);
                    echo "Failed to save product image.";
                }
                $lob->free();
                header("Location: traderInterface.php");
            } else {
                $error = oci_error($statement);
                echo "Error adding product: " . $error['message'];
            }
        } else {
            echo "Failed to upload product image.";
        }
        
    }


    //  Updating product
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateproduct'])) {
        $productId = $_POST['update_productId'];
        $productName = $_POST['product-name'];
        $description = $_POST['description'];
        $price = $_POST['price'] ;
        $quantityPerItem = $_POST['quantity'];
        $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
        $minOrder = isset($_POST['min-order']) ? $_POST['min-order'] : '';
        $maxOrder = isset($_POST['max-order']) ? $_POST['max-order'] : '';
        $allergyInfo = isset($_POST['allergy-info']) ? $_POST['allergy-info'] : '';
        $shop_id = $_SESSION['shop_id'];
        $category = isset($_POST['category']) ? $_POST['category'] : '';
   
        if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
            $productImage = $_FILES['product-image'];
            $base64ImageData = base64_encode(file_get_contents($productImage['tmp_name']));
            $decodedImageData = base64_decode($base64ImageData);
            $lob = oci_new_descriptor($conn, OCI_D_LOB);
        
            // Update the product in the database with a new BLOB for the image
            $query = "UPDATE PRODUCT
                      SET 
                          product_image = EMPTY_BLOB(),
                          product_name = :productName,
                          description = :description,
                          price = :price,
                          quantity_per_item = :quantityPerItem,
                          stock = :stock,
                          min_order = :minOrder,
                          max_order = :maxOrder,
                          allergy_info = :allergyInfo,
                          shop_id = :shop_id,
                          category_id = :category,
                          discount_id = 4001
                      WHERE 
                          product_id = :productId
                      RETURNING product_image INTO :imageData";
        
            $statement = oci_parse($conn, $query);
        
            // Bind the variables
            oci_bind_by_name($statement, ":productName", $productName);
            oci_bind_by_name($statement, ":description", $description);
            oci_bind_by_name($statement, ":price", $price);
            oci_bind_by_name($statement, ":quantityPerItem", $quantityPerItem);
            oci_bind_by_name($statement, ":stock", $stock);
            oci_bind_by_name($statement, ":minOrder", $minOrder);
            oci_bind_by_name($statement, ":maxOrder", $maxOrder);
            oci_bind_by_name($statement, ":allergyInfo", $allergyInfo);
            oci_bind_by_name($statement, ":shop_id", $shop_id);
            oci_bind_by_name($statement, ":category", $category);
            oci_bind_by_name($statement, ":productId", $productId);
            oci_bind_by_name($statement, ":imageData", $lob, -1, OCI_B_BLOB);
        
            // Execute the query
            $result = oci_execute($statement, OCI_DEFAULT);
            if ($result) {
                // Save the image data to the BLOB
                if ($lob->save($decodedImageData)) {
                    oci_commit($conn);
                    echo "Product updated successfully with image.";
                    echo '<meta http-equiv="refresh" content="3">';
                } else {
                    oci_rollback($conn);
                    echo "Failed to save product image.";
                }
                $lob->free();
                header("Location: traderInterface.php");
            } else {
                $error = oci_error($statement);
                echo "Error updating product: " . $error['message'];
            }
        } else {

            $query = "UPDATE PRODUCT
                      SET 
                          product_name = :productName,
                          description = :description,
                          price = :price,
                          quantity_per_item = :quantityPerItem,
                          stock = :stock,
                          min_order = :minOrder,
                          max_order = :maxOrder,
                          allergy_info = :allergyInfo,
                          shop_id = :shop_id,
                          category_id = :category,
                          discount_id = 4001
                      WHERE 
                          product_id = :productId";
        
            $statement = oci_parse($conn, $query);
        
            // Bind the variables
            oci_bind_by_name($statement, ":productName", $productName);
            oci_bind_by_name($statement, ":description", $description);
            oci_bind_by_name($statement, ":price", $price);
            oci_bind_by_name($statement, ":quantityPerItem", $quantityPerItem);
            oci_bind_by_name($statement, ":stock", $stock);
            oci_bind_by_name($statement, ":minOrder", $minOrder);
            oci_bind_by_name($statement, ":maxOrder", $maxOrder);
            oci_bind_by_name($statement, ":allergyInfo", $allergyInfo);
            oci_bind_by_name($statement, ":shop_id", $shop_id);
            oci_bind_by_name($statement, ":category", $category);
            oci_bind_by_name($statement, ":productId", $productId);
        
            // Execute the query
            $result = oci_execute($statement, OCI_DEFAULT);
            if ($result) {
                oci_commit($conn);
                echo "Product updated successfully without image.";
                echo '<meta http-equiv="refresh" content="3">';
                // header("Location: traderInterface.php");
            } else {
                $error = oci_error($statement);
                echo "Error updating product: " . $error['message'];
            }
        }
        
        
    }


    // Update Shop logic below:
    if (isset($_POST['submit']) && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateShop'])) {
        $shop_id =  $_SESSION['shop_id'];
        $shopName = $_POST['shop-name'];
        $email = $_POST['shop-email'];
        $description = $_POST['description'];

        if (isset($_FILES['shop-image']) && $_FILES['shop-image']['error'] == 0) {
            // Image is set, update all fields including the image
            $shopImage = $_FILES['shop-image'];
            $base64ImageData = base64_encode(file_get_contents($shopImage['tmp_name']));
            $decodedImageData = base64_decode($base64ImageData);

            $query2 = "UPDATE Shop 
                    SET SHOP_IMAGE = EMPTY_BLOB(), 
                        SHOP_NAME = :shopName, 
                        DESCRIPTION = :description, 
                        SHOP_EMAIL = :email, 
                        REGISTRATION_DATE = SYSDATE 
                    WHERE shop_id = :shop_id
                    RETURNING SHOP_IMAGE INTO :imageData";

            $statement_Shop = oci_parse($conn, $query2);

            // Bind the variables
            oci_bind_by_name($statement_Shop, ":shopName", $shopName);
            oci_bind_by_name($statement_Shop, ":description", $description);
            oci_bind_by_name($statement_Shop, ":email", $email);
            oci_bind_by_name($statement_Shop, ":shop_id", $shop_id);

            // Create a new descriptor for the BLOB
            $lob = oci_new_descriptor($conn, OCI_D_LOB);
            oci_bind_by_name($statement_Shop, ":imageData", $lob, -1, OCI_B_BLOB);

            // Execute the query
            $result = oci_execute($statement_Shop, OCI_DEFAULT);
            if ($result) {
                // Save the image data to the BLOB
                if ($lob->save($decodedImageData)) {
                    oci_commit($conn);
                    header("Location: traderInterface.php");
                    exit();
                } else {
                    oci_rollback($conn);
                    echo "Failed to save shop image.";
                }
                $lob->free();
            } else {
                echo "Error updating profile!";
            }
        } else {
            // Image is not set, update all fields except the image
            $query2 = "UPDATE Shop 
                    SET SHOP_NAME = :shopName, 
                        DESCRIPTION = :description, 
                        SHOP_EMAIL = :email, 
                        REGISTRATION_DATE = SYSDATE 
                    WHERE Shop_id = :shop_id";

            $statement_Shop = oci_parse($conn, $query2);

            // Bind the variables
            oci_bind_by_name($statement_Shop, ":shopName", $shopName);
            oci_bind_by_name($statement_Shop, ":description", $description);
            oci_bind_by_name($statement_Shop, ":email", $email);
            oci_bind_by_name($statement_Shop, ":shop_id", $shop_id);

            // Execute the query
            $result = oci_execute($statement_Shop);
            if ($result) {
                oci_commit($conn);
                header("Location: traderInterface.php");
                exit();
            } else {
                echo "Error updating profile!";
            }
        }

        oci_close($conn);
    }

?>
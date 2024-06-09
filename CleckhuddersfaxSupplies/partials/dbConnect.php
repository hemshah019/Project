<?php

// Database class definition
class Database
{
    private $conn;
    // Database connection
    public function __construct($username = 'hembikram', $password = 'Hem#123', $host = 'localhost', $dbname = 'xe')
    {
        $this->conn = oci_connect($username, $password, "//" . $host . "/" . $dbname);
        if (!$this->conn) {
            $m = oci_error();
            throw new Exception("Database connection failed: " . $m['message']);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
    
    public function closeConnection()
    {
        oci_close($this->conn);
    }

    public function executeQuery($query, $params = [])
    {
        $statement = oci_parse($this->conn, $query);
        if (!$statement) {
            $m = oci_error($this->conn);
            throw new Exception("Error preparing query: " . $m['message']);
        }

        // Bind parameters if provided
        foreach ($params as $key => $value) {
            oci_bind_by_name($statement, ":" . $key, $params[$key]);
        }

        if (!oci_execute($statement)) {
            $m = oci_error($statement);
            throw new Exception("Error executing query: " . $m['message']);
        }
        return $statement;
    }

    public function fetchRow($statement)
    {
        return oci_fetch_assoc($statement);
    }


    public function getProducts()
    {
        $products = array();

        try {
            $query = "SELECT * FROM (
                SELECT p.*, ROUND(r.average_rating, 2) AS rating
                FROM product p
                LEFT JOIN (
                  SELECT product_id, AVG(rating) AS average_rating
                  FROM review
                  GROUP BY product_id
                ) r ON p.product_id = r.product_id
                JOIN shop sh ON sh.shop_id = p.shop_id
                JOIN trader tr ON tr.trader_id = sh.trader_id
                WHERE UPPER(sh.status) = UPPER('Active')
                  AND UPPER(tr.ISVERIFIED) = UPPER('yes')
                  AND p.STOCK > 1
                ORDER BY DBMS_RANDOM.VALUE
              )
              WHERE ROWNUM <= 9";

            $statement = $this->executeQuery($query);

            while ($row = $this->fetchRow($statement)) {
                $products[] = $row;
            }

            $this->closeConnection();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $products;
    }

    public function getProductById($id)
    {
        $product = null;

        try {
            $query = "
        SELECT p.*, ROUND(r.average_rating, 2) AS rating, ct.category_name, sh.shop_name, ds.discount_percentage
        FROM product p
        LEFT JOIN (
                    SELECT product_id, AVG(rating) AS average_rating
                    FROM review
                    GROUP BY product_id
                    ) r ON p.product_id = r.product_id
        JOIN CATEGORY ct on p.category_id = ct.category_id
        join shop sh on sh.shop_id = p.shop_id
        JOIN DISCOUNT ds ON ds.discount_id = p.discount_id
        WHERE p.product_id = :id";

            $statement = $this->executeQuery($query, array("id" => $id));

            $product = $this->fetchRow($statement);

            $this->closeConnection();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $product;
    }


    public function getProductImage($id)
    {
        $query = 'SELECT PRODUCT_IMAGE FROM Product WHERE PRODUCT_ID = :id';
        $statement = oci_parse($this->conn, $query);
        oci_bind_by_name($statement, ":id", $id);

        if (!oci_execute($statement)) {
            $m = oci_error($statement);
            throw new Exception("Error executing query: " . $m['message']);
        }

        $row = oci_fetch_array($statement, OCI_ASSOC + OCI_RETURN_LOBS);

        if ($row && isset($row['PRODUCT_IMAGE'])) {
            $imageData = $row['PRODUCT_IMAGE'];
            $imageBase64 = base64_encode($imageData);
            return $imageBase64;
        } else {
            return '';
        }
    }


    public function getCartIdUsingCustomerId($id)
    {
        try {
            // Prepare the SQL query
            $query = 'SELECT cart_id FROM cart WHERE customer_id = :customer_id';
            $statement = oci_parse($this->conn, $query);

            oci_bind_by_name($statement, ':customer_id', $id);

            if (!oci_execute($statement)) {
                $m = oci_error($statement);
                throw new Exception("Error executing query: " . $m['message']);
            }

            $row = oci_fetch_array($statement, OCI_ASSOC);
            oci_free_statement($statement);

            if ($row && isset($row['CART_ID'])) {
                return $row['CART_ID'];
            } else {
                return null;
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }


    public function getCartItems($user_id)
    {
        $cartItems = array();

        try {
            $cartId = $this->getCartIdUsingCustomerId($user_id); 
            $query = "SELECT product_id, quantity, special_instruction FROM cart_product WHERE cart_id = :cart_id";

            $conn = $this->getConnection();

            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ":cart_id", $cartId);

            oci_execute($statement);

            // Fetch rows one by one
            while ($row = oci_fetch_assoc($statement)) {
                // Ensure that the fetched row has the required keys
                if (isset($row['PRODUCT_ID'], $row['QUANTITY'], $row['SPECIAL_INSTRUCTION'])) {
                    // Store the cart item with product_id as key in the cartItems array
                    $cartItems[$row['PRODUCT_ID']] = array(
                        'quantity' => $row['QUANTITY'],
                        'special_instruction' => $row['SPECIAL_INSTRUCTION']
                    );
                }
            }

            // Close the connection
            oci_close($conn);

        } catch (Exception $e) {
            // Handle exceptions
            throw new Exception("Error fetching cart items: " . $e->getMessage());
        }

        // Log debug information
        echo '<br>inside db getcartitems ';
        var_dump($cartItems);
        echo '<br>';

        return $cartItems;
    }


    public function updateCartItem($user_id, $product_id, $quantity, $special_instruction)
    {
        try {
            // Get the database connection
            $conn = $this->getConnection();
            $cartId = $this->getCartIdUsingCustomerId($user_id);

            // Prepare the SQL query
            $query = "UPDATE cart_product 
                      SET quantity = :quantity, special_instruction = :special_instruction 
                      WHERE cart_id = :cart_id AND product_id = :product_id";

            // Prepare the statement
            $statement = oci_parse($conn, $query);

            // Bind parameters
            oci_bind_by_name($statement, ":cart_id", $cartId);
            oci_bind_by_name($statement, ":product_id", $product_id);
            oci_bind_by_name($statement, ":quantity", $quantity);
            oci_bind_by_name($statement, ":special_instruction", $special_instruction);

            // Execute the statement
            if (!oci_execute($statement)) {
                $m = oci_error($statement);
                throw new Exception("Error executing query: " . $m['message']);
            }

            // Commit the transaction
            oci_commit($conn);

            // Free the statement
            oci_free_statement($statement);

            return true; // Return true on successful update
        } catch (Exception $e) {
            throw new Exception("Error updating cart item: " . $e->getMessage());
        }
    }

    public function insertCartItem($user_id, $product_id, $quantity, $special_instruction)
    {
        try {
            // Get the database connection
            $conn = $this->getConnection();
            $cartId = $this->getCartIdUsingCustomerId($user_id);

            // Prepare the SQL query
            $query = "INSERT INTO cart_product (cart_id, product_id, quantity, special_instruction) 
                  VALUES (:cart_id, :product_id, :quantity, :special_instruction)";

            // Prepare the statement
            $statement = oci_parse($conn, $query);

            // Bind parameters
            oci_bind_by_name($statement, ":cart_id", $cartId);
            oci_bind_by_name($statement, ":product_id", $product_id);
            oci_bind_by_name($statement, ":quantity", $quantity);
            oci_bind_by_name($statement, ":special_instruction", $special_instruction);

            // Execute the statement
            if (!oci_execute($statement)) {
                $m = oci_error($statement);
                throw new Exception("Error executing query: " . $m['message']);
            }

            // Commit the transaction
            oci_commit($conn);

            // Free the statement
            oci_free_statement($statement);

            return true; // Return true on successful update
        } catch (Exception $e) {
            throw new Exception("Error inserting cart item: " . $e->getMessage());
        }
    }

    public function getProductFromWishlist($customerId)
    {
        $wishlistProducts = array();
        try {
            $query = "SELECT p.*, c.category_name, s.shop_name
                  FROM product p 
                  JOIN favourite f ON p.product_id = f.product_id
                  JOIN category c ON p.category_id = c.category_id
                  JOIN shop s ON s.shop_id = p.shop_id
                  WHERE f.customer_id = :customer_id";
            $statement = $this->executeQuery($query, array("customer_id" => $customerId));
            while ($row = $this->fetchRow($statement)) {
                $wishlistProducts[] = $row;
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $wishlistProducts;
    }


    // From wishlist to cart in database when clicked addto cart.
    public function updateOrInsertCartItem($userId, $productId, $quantity, $specialInstruction)
    {
        $cartId = $this->getCartIdUsingCustomerId($userId);
        $conn = $this->getConnection();

        $query = "SELECT quantity, special_instruction FROM cart_product WHERE cart_id = :cart_id AND product_id = :product_id";
        $statement = oci_parse($conn, $query);

        oci_bind_by_name($statement, ":cart_id", $cartId);
        oci_bind_by_name($statement, ":product_id", $productId);

        oci_execute($statement);

        $existingCartItem = oci_fetch_assoc($statement);

        if ($existingCartItem) {
            $existingQuantity = $existingCartItem['QUANTITY'];
            $existingSpecialInstruction = $existingCartItem['SPECIAL_INSTRUCTION'];
            $newQuantity = $existingQuantity + $quantity;
            $this->updateCartItem($userId, $productId, $newQuantity, $specialInstruction);
            return;
        } else {
            $this->insertCartItem($userId, $productId, 1, '');
            return;
        }
    }


    // Update favourtite table when clicked add to wishlist on a product.
    public function addToWishlist($productId, $customerId)
    {
        $conn = $this->getConnection();

        // Check if the product already exists in the wishlist
        $query = "SELECT * FROM Favourite WHERE product_id = :product_id AND customer_id = :customer_id";
        $statement = oci_parse($conn, $query);
        oci_bind_by_name($statement, ":product_id", $productId);
        oci_bind_by_name($statement, ":customer_id", $customerId);
        oci_execute($statement);

        $existingProduct = oci_fetch_assoc($statement);
        if ($existingProduct) {
            // Product already exists in the wishlist
            return;
        }

        // Insert the product into the wishlist
        $query = "INSERT INTO Favourite (product_id, customer_id) VALUES (:product_id, :customer_id)";
        $statement = oci_parse($conn, $query);
        oci_bind_by_name($statement, ":product_id", $productId);
        oci_bind_by_name($statement, ":customer_id", $customerId);
        oci_execute($statement);
    }


    // Function to fetch reviews for a specific product
    public function getReviewsForAProduct($productId)
    {
        $reviews = array();
        try {
            $query = "SELECT r.comments AS review_text, r.rating, r.reviewed_date AS review_date, 
                         c.first_name || ' ' || c.last_name AS customer_name
                  FROM review r
                  JOIN customer c ON r.customer_id = c.customer_id
                  WHERE r.product_id = :product_id
                  ORDER BY r.reviewed_date DESC";
            $statement = $this->executeQuery($query, array("product_id" => $productId));
            while ($row = $this->fetchRow($statement)) {
                $reviews[] = $row;
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $reviews;
    }


    public function getProductsByShopId($shopId)
    {
        $products = array();

        try {
            $query = "SELECT p.*, ROUND(r.average_rating, 2) AS rating
                  FROM product p
                  LEFT JOIN (
                      SELECT product_id, AVG(rating) AS average_rating
                      FROM review
                      GROUP BY product_id
                  ) r ON p.product_id = r.product_id
                  JOIN shop sh ON sh.shop_id = p.shop_id
                  WHERE sh.shop_id = :shop_id";

            $statement = $this->executeQuery($query, array("shop_id" => $shopId));

            while ($row = $this->fetchRow($statement)) {
                $products[] = $row;
            }

            $this->closeConnection();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $products;
    }


    // Remove the product from wishlist after the product is added into the cart.
    public function removeFromWishlist($customerId, $productId)
    {
        $conn = $this->getConnection();
        $query = "DELETE FROM favourite WHERE customer_id = :customer_id AND product_id = :product_id";

        $statement = oci_parse($conn, $query);

        oci_bind_by_name($statement, ":customer_id", $customerId);
        oci_bind_by_name($statement, ":product_id", $productId);

        if (oci_execute($statement)) {
            return;
        } else {
            return;
        }
    }

    
    // Function to fetch order details
    function getOrderDetailsFromDatabase($customerId) {
        $conn = $this->getConnection();
        
        $orders = array();
        
        try {
            // Your SQL query to fetch order details -->
            $query = "SELECT ord.ORDER_ID, ord.ORDER_STATUS, TO_CHAR(order_date, 'DD-MON-YY HH:MI:SS AM') as order_date, op.ORDER_PRODUCT_ID, op.SPECIAL_INSTRUCTION, op.QUANTITY, 
                             p.PRODUCT_ID, p.PRODUCT_NAME, p.PRICE, d.DISCOUNT_PERCENTAGE
                      FROM orders ord
                      JOIN order_product op ON ord.ORDER_ID = op.ORDER_ID
                      JOIN product p ON op.PRODUCT_ID = p.PRODUCT_ID
                      JOIN discount d ON d.DISCOUNT_ID = p.DISCOUNT_ID
                      WHERE ord.CUSTOMER_ID = :customer_id
                      ORDER BY ord.ORDER_ID DESC";
    
            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ':customer_id', $customerId);
            oci_execute($statement);
    
            // Loop through the results to organize them by order ID
            while ($row = oci_fetch_assoc($statement)) {
                $orderId = $row['ORDER_ID'];
                // If the order ID is not already present in the array, initialize it
                if (!isset($orders[$orderId])) {
                    $orders[$orderId] = array(
                        'order_id' => $orderId,
                        'status' => $row['ORDER_STATUS'],
                        'order_date' => $row['ORDER_DATE'],
                        'order_products' => array()
                    );
                }
    
                // Add product details to the corresponding order ID
                $orders[$orderId]['order_products'][] = array(
                    'product_id' => $row['PRODUCT_ID'],
                    'product_name' => $row['PRODUCT_NAME'],
                    'price' => $row['PRICE'],
                    'discount' => $row['DISCOUNT_PERCENTAGE'],
                    'special_instruction' => $row['SPECIAL_INSTRUCTION'],
                    'quantity' => $row['QUANTITY']
                );
            }
    
            oci_free_statement($statement);
    
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    
        return $orders;
    }


    public function getCustomerById($customerId) {
        $customer = array();
        try {
            $query = "SELECT * FROM customer WHERE CUSTOMER_ID = :customerId";
            $statement = oci_parse($this->conn, $query);
            oci_bind_by_name($statement, ":customerId", $customerId);
            oci_execute($statement);
            $customer = oci_fetch_assoc($statement);
            oci_free_statement($statement);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $customer;
    }

    public function getProfileImage($customerId)
    {
        $query = "SELECT cust_image FROM customer WHERE customer_id = :customerId";
        try {
            $statement = oci_parse($this->conn, $query);
            oci_bind_by_name($statement, ":customerId", $customerId);

            if (!oci_execute($statement)) {
                $m = oci_error($statement);
                throw new Exception("Error executing query: " . $m['message']);
            }

            $row = oci_fetch_array($statement, OCI_ASSOC + OCI_RETURN_LOBS);

            if ($row !== false && isset($row['CUST_IMAGE'])) {
                $imageData = $row['CUST_IMAGE'];
                $imageBase64 = base64_encode($imageData);
                return $imageBase64;
            } else {
                return null; 
            }
        } catch (Exception $e) {
            // Handle the exception and return null
            return null;
        }
    }




    public function insertProfileImage($customerId, $imageData)
    {
        try {
            // Initialize the LOB locator
            $lob = oci_new_descriptor($this->conn, OCI_D_LOB);
    
            // Prepare the query
            $query = "UPDATE customer SET cust_image = EMPTY_BLOB() WHERE customer_id = :customerId RETURNING cust_image INTO :imageData";
            $statement = oci_parse($this->conn, $query);
    
            // Bind parameters
            oci_bind_by_name($statement, ":customerId", $customerId);
            oci_bind_by_name($statement, ":imageData", $lob, -1, OCI_B_BLOB);
    
            // Execute the query
            if (!oci_execute($statement, OCI_DEFAULT)) {
                $m = oci_error($statement);
                throw new Exception("Error executing query: " . $m['message']);
            }
    
            // Write the image data to the BLOB
            if ($lob->save($imageData)) {
                oci_commit($this->conn);
                $lob->free();
                return true;
            } else {
                oci_rollback($this->conn);
                $lob->free();
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    public function updateCustomerProfile($customerId, $updatedData) {
        $setClause = [];
        $params = [];

        var_dump($updatedData);
        
        foreach ($updatedData as $column => $value) {
            $setClause[] = "$column = :$column";
            $params[":$column"] = $value;
        }
        
        $params[':customerId'] = $customerId;
        $setClause = implode(", ", $setClause);
        $query = "UPDATE customer SET $setClause WHERE customer_id = :customerId";
        
        $statement = oci_parse($this->conn, $query);
        if (!$statement) {
            $m = oci_error($this->conn);
            throw new Exception("Error preparing query: " . $m['message']);
        }
        
        foreach ($params as $param => $value) {
            oci_bind_by_name($statement, $param, $params[$param]);
        }
        
        if (!oci_execute($statement)) {
            $m = oci_error($statement);
            throw new Exception("Error executing query: " . $m['message']);
        }
        
        return true;
    }
    

    public function addReview($customerId, $productId, $rating, $comments) {
        try {
            // Prepare the SQL query
            $query = "INSERT INTO review (CUSTOMER_ID, PRODUCT_ID, RATING, COMMENTS, REVIEWED_DATE)
                      VALUES (:customerId, :productId, :rating, :comments, SYSDATE)";
    
            // Bind parameters and execute the query

            echo 'here in db rev: ' .$customerId;
            $statement = $this->executeQuery($query, array(
                "customerId" => $customerId,
                "productId" => $productId,
                "rating" => $rating,
                "comments" => $comments
            ));
    
            return true; 
        } catch (Exception $e) {
            return false; 
        }
    }

    public function updatePassword($email, $password) {
        try {
            $password = md5($password);
            $query = "UPDATE customer SET password = :password WHERE email = :email";
            $statement = $this->executeQuery($query, array(
                "password" => $password,
                "email" => $email
            ));
            return true;
        } catch (Exception $e) {
            echo "Error updating password: " . $e->getMessage();
            return false;
        }
    }


    public function getEmailByCustomerId($customerId)
    {
        try {
            $query = 'SELECT email FROM customer WHERE customer_id = :customer_id';
            
            $statement = $this->executeQuery($query, array('customer_id' => $customerId));
            $row = $this->fetchRow($statement);
            oci_free_statement($statement);
            if ($row && isset($row['EMAIL'])) {
                return $row['EMAIL'];
            } else {
                return null;
            }
        } catch (Exception $e) {
            throw new Exception("Error fetching email by customer ID: " . $e->getMessage());
        }
    }


    public function getTraderIdByUsername($username)
    {
        try {
            $query = 'SELECT trader_id FROM trader WHERE username = :username';
            
            $statement = $this->executeQuery($query, array('username' => $username));
            $row = $this->fetchRow($statement);
            oci_free_statement($statement);
            if ($row && isset($row['TRADER_ID'])) {
                return $row['TRADER_ID'];
            } else {
                return null;
            }
        } catch (Exception $e) {
            throw new Exception("Error fetching trader ID by username: " . $e->getMessage());
        }
    }


    public function getProductRatingByCustomerId($customerId)
    {
        $products = array();
        $conn = $this->getConnection();
        try {
            $query = "SELECT p.product_name, p.product_id, r.rating, r.review_id, r.comments, TO_CHAR(r.reviewed_date, 'YYYY-MM-DD HH24:MI:SS') as reviewed_date
                    FROM product p
                    JOIN review r 
                    ON p.product_id = r.product_id
                    WHERE r.customer_id = :customer_id
                    ORDER BY r.reviewed_date DESC";

            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ":customer_id", $customerId);
            oci_execute($statement);
            while ($row = oci_fetch_assoc($statement)) {
                $products[] = $row;
            }
            oci_free_statement($statement);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $products;
    }

    public function deleteReviewByReviewId($reviewId)
    {
        $conn = $this->getConnection();
        $query = "DELETE FROM review WHERE review_id = :review_id";
        $statement = oci_parse($conn, $query);
        oci_bind_by_name($statement, ":review_id", $reviewId);
        if (oci_execute($statement)) {
            return;
        } else {
            return;
        }
    }

    public function getShopIdByTraderUsername($username) 
    {
        $conn = $this->getConnection();
        $query = "SELECT sh.shop_id
                  FROM shop sh
                  JOIN trader tr ON sh.trader_id = tr.trader_id
                  WHERE tr.username = :username";
        $statement = oci_parse($conn, $query);
        oci_bind_by_name($statement, ":username", $username);

        if (oci_execute($statement)) {
            if ($row = oci_fetch_assoc($statement)) {
                return $row['SHOP_ID']; 
            } else {
                return null; 
            }
        } else {
            $error = oci_error($statement); 
            return null; 
        }
    }



    public function getShopImage($shopId) {
        $query = 'SELECT SHOP_IMAGE FROM shop WHERE shop_id = :id';
        $statement = oci_parse($this->conn, $query);
        oci_bind_by_name($statement, ":id", $shopId);

        if (!oci_execute($statement)) {
            $m = oci_error($statement);
            throw new Exception("Error executing query: " . $m['message']);
        }

        $row = oci_fetch_array($statement, OCI_ASSOC + OCI_RETURN_LOBS);

        if ($row && isset($row['SHOP_IMAGE'])) {
            $imageData = $row['SHOP_IMAGE'];
            $imageBase64 = base64_encode($imageData);
            return $imageBase64;
        } else {
            return '';
        }
    }



    public function getCartProducts($cartId)
    {
        $cartProducts = array();

        try {
            // Prepare the SQL query to fetch cart products
            $query = "SELECT p.product_id, p.product_name, cp.quantity, p.price 
                    FROM cart_product cp
                    JOIN product p ON cp.product_id = p.product_id
                    WHERE cp.cart_id = :cart_id";
            $conn = $this->getConnection();
            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ":cart_id", $cartId);
            oci_execute($statement);
            while ($row = oci_fetch_assoc($statement)) {
                $cartProducts[] = $row;
            }
            oci_close($conn);
        } catch (Exception $e) {
            throw new Exception("Error fetching cart products: " . $e->getMessage());
        }
        return $cartProducts;
    }


    // Update cart quantity
    public function updateQuantity($cartId, $productId, $newQuantity)
    {
        try {
            $query = "UPDATE cart_product 
                    SET quantity = :newQuantity
                    WHERE cart_id = :cartId
                    AND product_id = :productId";
            $conn = $this->getConnection();
            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ":newQuantity", $newQuantity);
            oci_bind_by_name($statement, ":cartId", $cartId);
            oci_bind_by_name($statement, ":productId", $productId);
            $success = oci_execute($statement);
            oci_close($conn);

            return $success;
        } catch (Exception $e) {
            throw new Exception("Error updating cart quantity: " . $e->getMessage());
        }
    }


    // Removing a product from cart
    public function removeProductFromCart($cartId, $productId) {
        try {
            $query = "DELETE FROM cart_product 
                      WHERE cart_id = :cartId 
                      AND product_id = :productId";
            $conn = $this->getConnection();
            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ":cartId", $cartId);
            oci_bind_by_name($statement, ":productId", $productId);
            $success = oci_execute($statement);
            oci_close($conn);
    
            return $success;
        } catch (Exception $e) {
            throw new Exception("Error removing product from cart: " . $e->getMessage());
        }
    }


    public function getCustomerIdUsingUsername($username) {
        $query = "SELECT customer_id FROM customer WHERE username = :username";
        $conn = $this->getConnection();
        $statement = oci_parse($conn, $query);
        oci_bind_by_name($statement, ":username", $username);
        oci_execute($statement);

        $row = oci_fetch_assoc($statement);
        oci_close($conn);
        if ($row) {
            return $row['CUSTOMER_ID'];
        } else {
            return null;
        }
    }

    function isValidSlot($collection_date, $collection_slot) {
        $sql = "SELECT orders_count FROM collection_slot WHERE collection_date = TO_DATE(:collection_date, 'YYYY-MM-DD') AND collection_slot = :collection_slot";
        $conn = $this->getConnection();
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':collection_date', $collection_date);
        oci_bind_by_name($stmt, ':collection_slot', $collection_slot);
        oci_execute($stmt);
    
        $row = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
    
        if ($row) {
            return ($row['ORDERS_COUNT'] < 20);
        } else {
            return true;
        }
    }


    public function getAllShops() 
    {
        $conn = $this->getConnection();
        $query = "SELECT * FROM (SELECT * FROM shop ORDER BY shop_id) WHERE ROWNUM <= 5";
        $statement = oci_parse($conn, $query);
        oci_execute($statement);
        $shops = [];
        while ($row = oci_fetch_assoc($statement)) {
            $shops[] = $row;
        }
        oci_free_statement($statement);
        return $shops;
    }


       // Get stock of a particular product
       public function getProductStock($productId) {
        try {
            $query = "SELECT stock FROM product WHERE product_id = :productId";
            $conn = $this->getConnection();
            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ":productId", $productId);
            oci_execute($statement);
            $row = oci_fetch_assoc($statement);
            oci_close($conn);
    
            if ($row) {
                return $row['STOCK'];
            } else {
                throw new Exception("Product not found");
            }
        } catch (Exception $e) {
            throw new Exception("Error getting product stock: " . $e->getMessage());
        }
    }


    // update stock of a product
    public function updateProductStock($productId, $newStock) {
        try {
            $query = "UPDATE product SET stock = :newStock WHERE product_id = :productId";
            
            $conn = $this->getConnection();
            $statement = oci_parse($conn, $query);
            
            oci_bind_by_name($statement, ':newStock', $newStock);
            oci_bind_by_name($statement, ':productId', $productId);
            
            $result = oci_execute($statement, OCI_COMMIT_ON_SUCCESS);
            
            oci_free_statement($statement);
            $this->closeConnection();
            
            return $result;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }


    // Get order based on order id
    public function getOrderByOrderId($orderId) {
        $order = null;
    
        try {
            $query = "SELECT * FROM orders WHERE order_id = :order_id";
            $conn = $this->getConnection();
            $statement = oci_parse($conn, $query);
            oci_bind_by_name($statement, ':order_id', $orderId);
            oci_execute($statement);
            if ($row = oci_fetch_assoc($statement)) {
                $order = $row;
            }
            oci_free_statement($statement);
            $this->closeConnection();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $order;
    }  
}
?>
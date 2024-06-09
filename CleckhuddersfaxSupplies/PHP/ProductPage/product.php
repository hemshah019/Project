<?php
require_once '../../partials/dbConnect.php';
require_once '../alertService.php';

$db = new Database();
$conn = $db->getConnection();

$filterdata = !empty($_GET['q']) ? "AND PRODUCT.PRODUCT_NAME LIKE '%" . $_GET['q'] . "%'" : '';

$sortOptions = [
    'asec' => 'PRODUCT.PRODUCT_NAME ASC',
    'desc' => 'PRODUCT.PRODUCT_NAME DESC',
    'lowest' => 'PRODUCT.PRICE ASC',
    'highest' => 'PRODUCT.PRICE DESC'
];

$toSort = $_GET['advsort'] ?? '';
$query = !empty($toSort) && isset($sortOptions[$toSort]) ? "ORDER BY " . $sortOptions[$toSort] : '';

$advfilterdata = !empty($_GET['advsearch']) ? "AND PRODUCT.PRODUCT_NAME LIKE '%" . $_GET['advsearch'] . "%'" : '';

$cid = $_GET['category'] ?? '';
$advcidQuery = !empty($cid) ? "AND CATEGORY.CATEGORY_ID = $cid" : '';

$ratingQuery = !empty($_GET['rating']) ? intval($_GET['rating']) : TRUE;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleckhuddersfax Supplies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="product.css?<?php echo time();?>">
    <link rel="stylesheet" href="../HeaderPage/head.css">
    <link rel="stylesheet" href="../FooterPage/footer.css">
    <?php   AlertService::includeCSS(); ?>
</head>

<body>
        <?php
            AlertService::displayAlerts();
        ?>
    <div><?php include('../HeaderPage/head.php'); ?></div>

    <main class="page catalog-page mb-4">
        <section class="clean-block clean-catalog dark">
            <div class="container">
                <div class="block-heading">
                    <h1 class="heading">List of<span>Product</span></h1>
                </div>
                <div class="content">
                    <div class="row">
                        <div class="col-md-2 advance_search mb-3">
                            <form class="p-3" method="GET" id="advance_search">
                                <input class="form-control" type="search" placeholder="Search" aria-label="Search"
                                    name="advsearch" value="<?= htmlspecialchars($_GET['advsearch'] ?? '') ?>">
                                <div class="userBox">
                                    <select class="form-select mt-3" name="advsort">
                                        <option selected>Sort:</option>
                                        <option value="asec"
                                            <?= isset($_GET['advsort']) && $_GET['advsort'] == 'asec' ? 'selected' : '' ?>>
                                            Aa-Zz</option>
                                        <option value="desc"
                                            <?= isset($_GET['advsort']) && $_GET['advsort'] == 'desc' ? 'selected' : '' ?>>
                                            Zz-Aa</option>
                                        <option value="lowest"
                                            <?= isset($_GET['advsort']) && $_GET['advsort'] == 'lowest' ? 'selected' : '' ?>>
                                            Price: Low to High</option>
                                        <option value="highest"
                                            <?= isset($_GET['advsort']) && $_GET['advsort'] == 'highest' ? 'selected' : '' ?>>
                                            Price: High to Low</option>
                                    </select>
                                </div>

                                <div class="filter_category mt-3">
                                    <p class="my-2">Category:</p>
                                    <select class="form-select" name="category">
                                        <option selected value="">All Categories</option>
                                        <?php
                                        $categorysql = "SELECT * FROM Category";
                                        $categoryqry = oci_parse($conn, $categorysql);
                                        oci_execute($categoryqry);
                                        while ($row = oci_fetch_array($categoryqry)) {
                                            $cid = $row['CATEGORY_ID'];
                                            $selected = isset($_GET['category']) && $_GET['category'] == $cid ? "selected" : "";
                                            echo "<option value='$cid' $selected>" . htmlspecialchars($row['CATEGORY_NAME']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="filter_category mt-3">
                                    <p>Product Rating:</p>
                                    <select class="form-select" name="rating">
                                        <option selected value="">All Ratings</option>
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?= $i ?>"
                                            <?= isset($_GET['rating']) && $_GET['rating'] == $i ? 'selected' : '' ?>>
                                            <?= $i ?> Stars</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <button class="btn btn-dark mt-3" type="submit">Filter and Search</button>
                                <button class="btn btn-dark mt-3" type="button" onclick="clearForm()">Clear</button>
                            </form>
                        </div>

                        <div class="col-10 container">
                            <div class="products">
                                <div class="row g-5">
                                    <?php
                                    $sql = "
                                        SELECT PRODUCT.*, ROUND(r.average_rating, 2) AS rating
                                        FROM PRODUCT
                                        INNER JOIN CATEGORY ON CATEGORY.CATEGORY_ID = PRODUCT.CATEGORY_ID
                                        LEFT JOIN (
                                            SELECT PRODUCT_ID, AVG(RATING) AS average_rating
                                            FROM REVIEW
                                            GROUP BY PRODUCT_ID
                                        ) r ON PRODUCT.PRODUCT_ID = r.PRODUCT_ID
                                        JOIN shop sh on sh.shop_id = product.shop_id
                                        JOIN trader tr on tr.trader_id = sh.trader_id
                                        WHERE UPPER(sh.status) = upper('Active') and upper(tr.ISVERIFIED) = upper('yes')
                                        and PRODUCT.STOCK > 1 $advcidQuery $filterdata $advfilterdata $query";

                                    $qry = oci_parse($conn, $sql);
                                    oci_execute($qry);
                                    $total = oci_fetch_all($qry, $result);

                                    if ($total > 0) {
                                        oci_execute($qry);  // Re-execute query for fetching data
                                        while ($row = oci_fetch_array($qry)) {
                                            $productId = $row['PRODUCT_ID'];
                                            $imageBase64 = $db->getProductImage($productId);
                                            $price = "£" . $row['PRICE'];
                                            $avgRatings = $row['RATING'];
                                            $stock = $row['STOCK'] > 1 ? "<p class=\"text-success\">In Stock<p>" : "<p class=\"text-warning\">No Stock<p>";
                                            
                                            // Calculate rating stars
                                            $avgRating = ceil($avgRatings);
                                            $noRating = 5 - $avgRating;

                                            if ($ratingQuery === TRUE || $avgRating == $ratingQuery) {

                                                echo '
                                                <div class="col-12 col-md-6 col-lg-4">
                                                    <div class="card card-border d-flex">';
                                                    if ($imageBase64) {
                                                        echo '<a href="../HomePage/productdtl.php?product_id=' . $productId . '">';
                                                        echo '<img src="data:image/jpeg;base64,' . $imageBase64 . '" class="card-img-top" alt="product image" style="width: 100%; height: 240px;">';
                                                        echo '</a>';
                                                    } else {
                                                        echo '<a href="../HomePage/productdtl.php?product_id=' . $productId . '">';
                                                        echo '<img src="path_to_placeholder_image.jpg" class="card-img-top" alt="' . htmlspecialchars($row['PRODUCT_NAME']) . ' Image" style="width: 100%; height: auto;">';
                                                        echo '</a>';
                                                    }


                                                        echo '<div class="card-body">
                                                            <div class="card-upper-body text-left">
                                                                <h5 class="card-title"><a href="../HomePage/productdtl.php?product_id=' . $productId . '" style="color: #333; font-size: 1.75rem;">' . htmlspecialchars($row['PRODUCT_NAME']) . '</a></h5>
                                                                <h6 class="card-text text-muted" style="font-family: \'Roboto\', sans-serif; font-size: 1.5rem;">' . substr(htmlspecialchars($row['DESCRIPTION']), 0, 50) . '...</h6>
                                                                <h6 class="card-title" style="font-size: 1.5rem;">' . $price . '</h6>
                                                                ' . $stock . '
                                                            </div>
                                                            <div class="star d-flex">
                                                                ' . str_repeat('<i class="bi bi-star-fill me-1 text-warning"></i>', $avgRating) . '
                                                                ' . str_repeat('<i class="bi bi-star me-1"></i>', $noRating) . '
                                                            </div>
                                                            <div class="btn-group mt-2" role="group" aria-label="Product Actions">
                                                                <a href="../HomePage/addToCart.php?productid=' . $productId . '" class="btn btn-primary" style="font-family: \'Roboto\', sans-serif; font-size: 1.5rem; background-color: #ff8000; border: none;">Add to Cart</a>
                                                                <a href="../HomePage/addToWishlist.php?product_id=' . $productId . '" class="btn btn-outline-secondary ml-2" style="font-family: \'Roboto\', sans-serif; font-size: 1.5rem; background-color: #6c757d; border: none; color: #FFF">Add to Wishlist</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                                            }
                                        }
                                    } else {
                                        echo "<h5 class=\"ps-2\">No products found !<h5>";
                                    }
                                    oci_free_statement($qry);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include('../FooterPage/footer.php'); ?>
    
    <script src="../HeaderPage/head.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function clearForm() {
        document.getElementById("advance_search").reset();
        window.location.href = window.location.pathname;
    }
    </script>
</body>

</html>
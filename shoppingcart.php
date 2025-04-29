<?php
session_start();

$servername = "blaineproject";
$username = "root";
$password = "";
$dbname = "st_ronans_gaa";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Remove from Cart
if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
}

// Handle Update Quantity
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['quantity'];
    if ($new_quantity > 0) {
        $_SESSION['cart'][$product_id] = $new_quantity;
    }
}

// Calculate total
function calculateTotal($cart, $products) {
    $total = 0;
    foreach ($cart as $id => $qty) {
        $product = array_filter($products, function($p) use ($id) {
            return $p['id'] == $id;
        });
        $product = reset($product);
        $total += $product['price'] * $qty;
    }
    return $total;
}

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

// Prepare the products array
$products = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

// Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 0;
    }
    
    $_SESSION['cart'][$product_id] += $quantity;
}

$cart_total = calculateTotal($_SESSION['cart'], $products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Merchandise - St Ronan's GAA</title>
    <link rel="icon" type="image/png" href="images/st_ronans.png" />
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/shoppingcart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
</head>
<body>
    <!-- Navbar HTML -->
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="teams.php">Teams</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="fixtures.php">Fixtures</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <header class="header">
        <h1><i class="fas fa-shopping-bag"></i> Club Merchandise</h1>
        <p>Support the club with official St Ronan's GAA gear</p>
    </header>

    <div class="container">
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <div class="product-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="price">€<?php echo number_format($product['price'], 2); ?></p>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart">
            <h2><i class="fas fa-shopping-cart"></i> Your Cart</h2>
            <?php if (!empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $id => $qty): 
                    $product = array_filter($products, function($p) use ($id) {
                        return $p['id'] == $id;
                    });
                    $product = reset($product);
                ?>
                    <div class="cart-item">
                        <div>
                            <strong><?php echo $product['name']; ?></strong>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                <input type="number" name="quantity" value="<?php echo $qty; ?>" min="1" class="quantity-input">
                                <button type="submit" name="update_quantity" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Update
                                </button>
                                <button type="submit" name="remove_from_cart" class="btn btn-secondary">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                        <div>
                            €<?php echo number_format($product['price'] * $qty, 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="cart-total">
                    <strong>Total: €<?php echo number_format($cart_total, 2); ?></strong>
                    <button onclick="checkout()" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                    </button>
                </div>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function checkout() {
            if (confirm('Ready to complete your purchase?')) {
                window.location.href = 'checkout.php';
            }
        }
    </script>
</body>
</html>

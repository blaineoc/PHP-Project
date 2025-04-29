<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "st_ronans_gaa";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: shoppingcart.php");
    exit();
}

$sql = "SELECT id, name, price FROM products";
$result = mysqli_query($conn, $sql);
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[$row['id']] = $row;
}

function calculateTotal($cart, $products) {
    $total = 0;
    foreach ($cart as $id => $qty) {
        if (isset($products[$id])) {
            $total += $products[$id]['price'] * $qty;
        }
    }
    return $total;
}

$total = calculateTotal($_SESSION['cart'], $products);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    
    if (empty($name) || empty($email) || empty($address)) {
        $error = "All fields are required.";
    } else {
        $order_date = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, email, address, total, order_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $name, $email, $address, $total, $order_date);
        
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            
            $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $price = $products[$product_id]['price'];
                $stmt_items->bind_param("iiid", $order_id, $product_id, $quantity, $price);
                $stmt_items->execute();
            }
            
            unset($_SESSION['cart']);
            header("Location: checkout.php?success=1&order_id=" . $order_id);
            exit();
        } else {
            $error = "Error processing order. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - St Ronan's GAA</title>
    <link rel="icon" type="image/png" href="images/st_ronans.png" />
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="teams.php">Teams</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="fixtures.php">Fixtures</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="shoppingcart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        </ul>
    </nav>

    <header class="header">
        <h1><i class="fas fa-credit-card"></i> Checkout</h1>
        <p>Complete your purchase with St Ronan's GAA</p>
    </header>

    <div class="container">
        <div class="checkout-container">
            <?php if (isset($_GET['success'])): ?>
                <div class="success">
                    <h2><i class="fas fa-check-circle"></i> Order Placed Successfully!</h2>
                    <p>Your order number is #<?php echo $_GET['order_id']; ?></p>
                    <p>Thank you for your purchase. We'll process your order soon!</p>
                    <a href="shoppingcart.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
                </div>
            <?php else: ?>
                <?php if (isset($error)): ?>
                    <div class="error"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <h2>Shipping Information</h2>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="your.email@example.com">
                    </div>
                    <div class="form-group">
                        <label for="address">Delivery Address</label>
                        <textarea id="address" name="address" rows="4" required placeholder="Enter your full address"></textarea>
                    </div>

                    <div class="order-summary">
                        <h3>Your Order</h3>
                        <?php foreach ($_SESSION['cart'] as $id => $qty): ?>
                            <?php if (isset($products[$id])): ?>
                                <div class="cart-item">
                                    <span><?php echo $products[$id]['name']; ?> (x<?php echo $qty; ?>)</span>
                                    <span>€<?php echo number_format($products[$id]['price'] * $qty, 2); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <div class="cart-total">
                            <strong>Total: €<?php echo number_format($total, 2); ?></strong>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <button type="submit" name="place_order" class="btn btn-primary">
                            <i class="fas fa-check"></i> Place Order
                        </button>
                        <a href="shoppingcart.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Cart
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>

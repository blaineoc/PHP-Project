<?php
$servername = "blaineproject";
$username = "root";
$password = "";
$dbname = "st_ronans_gaa";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination settings
$results_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Search functionality (optional)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_sql = $search ? "WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%'" : '';
$sql = "SELECT * FROM members $search_sql LIMIT $start_from, $results_per_page";
$result = $conn->query($sql);

// Total number of results for pagination
$total_sql = "SELECT COUNT(*) FROM members $search_sql";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_row();
$total_pages = ceil($total_row[0] / $results_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members | Gaelic Games Club</title>
    <link rel="icon" type="image/png" href="images/st_ronans.png" />
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <header>
        <h1>Club Members</h1>
    </header>

    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="teams.php">Teams</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="fixtures.php">Fixtures</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="shoppingcart.php">Cart</a></li>
        </ul>
    </nav>

    <div class="container">
        <!-- Search Bar -->
        <div class="search-bar">
            <form action="members.php" method="GET">
                <input type="text" name="search" placeholder="Search Members..." value="<?= htmlspecialchars($search) ?>" />
                <button type="submit">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th><a href="?sort=name">First Name</a></th>
                    <th><a href="?sort=name">Last Name</a></th>
                    <th><a href="?sort=email">Email</a></th>
                    <th><a href="?sort=phone">Phone</a></th>
                    <th><a href="?sort=membership_date">Membership Date</a></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['membership_date']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No members found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i == $page) ? 'active' : '';
                echo "<a href='members.php?page=$i&search=" . urlencode($search) . "' class='$active'>$i</a> ";
            }
            ?>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>

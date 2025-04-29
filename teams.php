<!-- Teams Page (teams.php) -->
<?php
$servername = "blaineproject";
$username = "root";
$password = "";
$dbname = "st_ronans_gaa";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch teams from the database
$sql = "SELECT * FROM teams";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams - Gaelic Games Club</title>
    <link rel="icon" type="image/png" href="images/st_ronans.png" />
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        header {
            background: #2c3e50;
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 24px;
        }

        nav ul {
            list-style: none;
            padding: 0;
            text-align: center;
            background: #34495e;
            margin: 0;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            padding: 10px;
        }

        nav ul li a:hover {
            background: #2980b9;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #3498db;
            color: white;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        tr:hover {
            background: #ddd;
        }
    </style>
</head>

<body>

    <header>
        Gaelic Games Club - Teams
    </header>

    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="fixtures.php">Fixtures</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="shoppingcart.php">Cart</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2>Teams</h2>

        <table>
            <tr>
                <th>Team Name</th>
                <th>Team Captain</th>
                <th>Team Members</th>
                <th>Creation Date</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>

</html>

<?php
$conn->close();
?>

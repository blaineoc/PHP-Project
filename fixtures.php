<?php
$servername = "localhost"; // Updated to match your working setup
$username = "root";
$password = "";
$dbname = "st_ronans_gaa";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle adding a new fixture
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_fixture'])) {
    $our_team_id = $_POST['our_team_id'];
    $their_team_id = $_POST['their_team_id'];
    $fixture_date = $_POST['fixture_date'];
    $location = $_POST['location'];
    $event_id = $_POST['event_id'];

    // Ensure all fields are filled
    if (!empty($our_team_id) && !empty($their_team_id) && !empty($fixture_date) && !empty($location) && !empty($event_id)) {
        $stmt = $conn->prepare("INSERT INTO fixtures (event_id, our_team_id, their_team_id, location, fixture_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $event_id, $our_team_id, $their_team_id, $location, $fixture_date);

        if ($stmt->execute()) {
            $message = "<div class='alert success'><i class='fas fa-check-circle'></i> Fixture added successfully!</div>";
        } else {
            $message = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Please fill all fields.</div>";
    }
}

// Handle removing a fixture
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_fixture'])) {
    $fixture_id = $_POST['fixture_id'];
    $stmt = $conn->prepare("DELETE FROM fixtures WHERE fixture_id = ?");
    $stmt->bind_param("i", $fixture_id);

    if ($stmt->execute()) {
        $message = "<div class='alert success'><i class='fas fa-check-circle'></i> Fixture removed successfully!</div>";
    } else {
        $message = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error removing fixture: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Fetch fixtures
$sql = "
SELECT f.fixture_id, e.name AS event_name, t1.name AS our_team_name, t2.team_name AS their_team_name, f.location, f.fixture_date
FROM fixtures f
JOIN events e ON f.event_id = e.event_id
JOIN teams t1 ON f.our_team_id = t1.team_id
JOIN roscommon_teams t2 ON f.their_team_id = t2.team_id
ORDER BY f.fixture_date ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixtures | St Ronan's GAA</title>
    <link rel="icon" type="image/png" href="images/st_ronans.png" />
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fixtures.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="header">
        St Ronan's GAA - Fixtures
    </div>
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="teams.php">Teams</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="shoppingcart.php">Cart</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php if (isset($message)) echo $message; ?>
        <h1>Upcoming Fixtures</h1>

        <!-- Search and Filter Section -->
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search Fixtures..." oninput="filterFixtures()">
            <select id="sortOptions" onchange="sortFixtures()">
                <option value="date">Sort by Date</option>
                <option value="team">Sort by Team</option>
            </select>
            <button onclick="searchFixtures()">Search</button>
        </div>

        <!-- Fixtures Table -->
        <table id="fixturesTable">
            <thead>
                <tr>
                    <th>Fixture ID</th>
                    <th>Event</th>
                    <th>Home Team</th>
                    <th>Away Team</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['fixture_id']) . "</td>
                            <td>" . htmlspecialchars($row['event_name']) . "</td>
                            <td>" . htmlspecialchars($row['our_team_name']) . "</td>
                            <td>" . htmlspecialchars($row['their_team_name']) . "</td>
                            <td>" . htmlspecialchars($row['fixture_date']) . "</td>
                            <td>" . htmlspecialchars($row['location']) . "</td>
                            <td>
                                <form method='POST' action='fixtures.php' onsubmit=\"return confirm('Are you sure you want to remove this fixture?');\">
                                    <input type='hidden' name='fixture_id' value='" . $row['fixture_id'] . "'>
                                    <button type='submit' name='remove_fixture' class='btn-remove'>
                                        <i class='fas fa-trash'></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No fixtures found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Add New Fixture Form -->
        <div class="add-form">
            <h2>Add New Fixture</h2>
            <form method="POST" action="fixtures.php">
                <input type="hidden" name="add_fixture" value="1">
                
                <!-- Our Team Dropdown -->
                <select name="our_team_id" required>
                    <option value="">Select Our Team</option>
                    <?php
                    $teams_query = "SELECT team_id, name FROM teams";
                    $teams_result = $conn->query($teams_query);
                    while ($team = $teams_result->fetch_assoc()) {
                        echo "<option value='" . $team['team_id'] . "'>" . htmlspecialchars($team['name']) . "</option>";
                    }
                    ?>
                </select>

                <!-- Opponent Team Dropdown (From roscommon_teams) -->
                <select name="their_team_id" required>
                    <option value="">Select Opponent Team</option>
                    <?php
                    $roscommon_teams_query = "SELECT team_id, team_name FROM roscommon_teams";  // Fetch all teams from roscommon_teams
                    $roscommon_teams_result = $conn->query($roscommon_teams_query);
                    while ($team = $roscommon_teams_result->fetch_assoc()) {
                        echo "<option value='" . $team['team_id'] . "'>" . htmlspecialchars($team['team_name']) . "</option>";
                    }
                    ?>
                </select>

                <!-- Location Dropdown (Club Name + "Pitch") -->
                <select name="location" required>
                    <option value="">Select Location</option>
                    <option value="St Ronans">St Ronans Pitch</option> <!-- Make sure St Ronans Pitch is first -->
                    <?php
                    $roscommon_teams_result->data_seek(0); // Reset pointer to the roscommon_teams results
                    while ($team = $roscommon_teams_result->fetch_assoc()) {
                        $club_name = htmlspecialchars($team['team_name']);
                        echo "<option value='" . $club_name . "'>" . $club_name . " Pitch</option>";
                    }
                    ?>
                </select>

                <!-- Other fields -->
                <input type="date" name="fixture_date" required>

                <!-- Updated Event Dropdown -->
                <select name="event_id" required>
                    <option value="">Select Event</option>
                    <?php
                    $events_query = "SELECT event_id, name FROM events WHERE name != 'Gala' AND name != 'Meeting'"; // Exclude Gala and Meeting
                    $events_result = $conn->query($events_query);
                    while ($event = $events_result->fetch_assoc()) {
                        echo "<option value='" . $event['event_id'] . "'>" . htmlspecialchars($event['name']) . "</option>";
                    }
                    ?>
                    <option value="League Game">League Game</option> <!-- Add League Game -->
                </select>

                <button type="submit" class="btn-add">Add Fixture</button>
            </form>
        </div>
    </div>

    <script>
        function filterFixtures() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#fixturesTable tr:not(:first-child)');
            rows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                const homeTeam = cells[2].innerText.toLowerCase();
                const awayTeam = cells[3].innerText.toLowerCase();
                const location = cells[5].innerText.toLowerCase();
                row.style.display = (homeTeam.includes(searchTerm) || awayTeam.includes(searchTerm) || location.includes(searchTerm)) ? '' : 'none';
            });
        }

        function sortFixtures() {
            const sortOption = document.getElementById('sortOptions').value;
            const table = document.getElementById('fixturesTable');
            const rows = Array.from(table.rows).slice(1);

            if (sortOption === 'date') {
                rows.sort((a, b) => new Date(a.cells[4].innerText) - new Date(b.cells[4].innerText));
            } else if (sortOption === 'team') {
                rows.sort((a, b) => {
                    const teamA = a.cells[2].innerText + a.cells[3].innerText;
                    const teamB = b.cells[2].innerText + b.cells[3].innerText;
                    return teamA.localeCompare(teamB);
                });
            }

            rows.forEach(row => table.appendChild(row));
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>

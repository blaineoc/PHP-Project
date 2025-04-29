<?php
session_start();

// Initialize variables
$message = "";
$events = [];
$upcoming_events = [];
$past_events = [];

$servername = "localhost"; // Updated to match your working setup
$username = "root";
$password = "";
$dbname = "st_ronans_gaa";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding new events
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    $name = trim($_POST['name']);
    $event_date = $_POST['event_date'];
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $event_time = $_POST['event_time'];
    $event_type = $_POST['event_type'];

    if (!empty($name) && !empty($event_date) && !empty($location)) {
        $stmt = $conn->prepare("INSERT INTO events (name, event_date, event_time, location, description, event_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $event_date, $event_time, $location, $description, $event_type);

        if ($stmt->execute()) {
            $message = "<div class='alert success'><i class='fas fa-check-circle'></i> Event added successfully!</div>";
        } else {
            $message = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Handle event removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_event'])) {
    $event_id = $_POST['event_id'];
    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        $message = "<div class='alert success'><i class='fas fa-check-circle'></i> Event removed successfully!</div>";
    } else {
        $message = "<div class='alert error'><i class='fas fa-exclamation-circle'></i> Error removing event: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Fetch events and categorize them
$current_date = date('Y-m-d');
$sql = "SELECT *, DATE_FORMAT(event_date, '%D %M %Y') as formatted_date FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['event_date'] >= $current_date) {
            $upcoming_events[] = $row;
        } else {
            $past_events[] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - St Ronan's GAA Club</title>
    <link rel="icon" type="image/png" href="images/st_ronans.png" />
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="teams.php">Teams</a></li>
            <li><a href="fixtures.php">Fixtures</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="shoppingcart.php">Cart</a></li>
        </ul>
    </nav>

    <div class="page-header">
        <h1><i class="fas fa-calendar-alt"></i> Club Events</h1>
        <p>Stay up to date with all our upcoming events and activities</p>
    </div>

    <div class="container">
        <?php echo $message; ?>

        <div class="events-grid">
            <div class="events-list">
                <div class="event-tabs">
                    <button class="tab-btn active" onclick="showEvents('upcoming')">Upcoming Events</button>
                    <button class="tab-btn" onclick="showEvents('past')">Past Events</button>
                </div>

                <div id="upcoming-events">
                    <?php foreach ($upcoming_events as $event): ?>
                        <div class="event-card">
                            <div class="event-date">
                                <i class="far fa-calendar"></i> <?php echo $event['formatted_date']; ?>
                                <?php if($event['event_time']): ?>
                                    at <?php echo $event['event_time']; ?>
                                <?php endif; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                            <span class="event-type"><?php echo htmlspecialchars($event['event_type']); ?></span>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                            <?php if($event['description']): ?>
                                <p><?php echo htmlspecialchars($event['description']); ?></p>
                            <?php endif; ?>
                            <form method="POST" action="events.php" onsubmit="return confirm('Are you sure you want to remove this event?');">
                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                <button type="submit" name="remove_event" class="btn-remove">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="past-events" style="display: none;">
                    <?php foreach ($past_events as $event): ?>
                        <div class="event-card">
                            <div class="event-date">
                                <i class="far fa-calendar"></i> <?php echo $event['formatted_date']; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                            <span class="event-type"><?php echo htmlspecialchars($event['event_type']); ?></span>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                            <form method="POST" action="events.php" onsubmit="return confirm('Are you sure you want to remove this event?');">
                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                <button type="submit" name="remove_event" class="btn-remove">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="add-event-form">
                <h2>Add New Event</h2>
                <form method="POST" action="events.php">
                    <input type="hidden" name="add_event" value="1">
                    <div class="form-group">
                        <label>Event Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Event Type</label>
                        <select name="event_type" class="form-control" required>
                            <option value="Match">Match</option>
                            <option value="Training">Training</option>
                            <option value="Meeting">Meeting</option>
                            <option value="Social">Social Event</option>
                            <option value="Fundraiser">Fundraiser</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="event_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" name="event_time" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-plus-circle"></i> Add Event
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showEvents(type) {
            const upcomingEvents = document.getElementById('upcoming-events');
            const pastEvents = document.getElementById('past-events');
            const tabs = document.querySelectorAll('.tab-btn');

            tabs.forEach(tab => tab.classList.remove('active'));

            if (type === 'upcoming') {
                upcomingEvents.style.display = 'block';
                pastEvents.style.display = 'none';
                tabs[0].classList.add('active');
            } else {
                upcomingEvents.style.display = 'none';
                pastEvents.style.display = 'block';
                tabs[1].classList.add('active');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            showEvents('upcoming');
        });
    </script>
</body>
</html>

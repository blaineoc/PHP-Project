<?php
$servername = "blaineproject";
$username = "root";
$password = "";
$dbname = "Gaelic_Games_Club";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$event_id = $_POST['event_id'];
$home_team_id = $_POST['home_team_id'];
$away_team_id = $_POST['away_team_id'];
$fixture_date = $_POST['fixture_date'];
$location = $_POST['location'];

// Insert the new fixture into the database
$sql = "INSERT INTO fixtures (event_id, home_team_id, away_team_id, fixture_date, location)
        VALUES ('$event_id', '$home_team_id', '$away_team_id', '$fixture_date', '$location')";

if ($conn->query($sql) === TRUE) {
    echo "New fixture added successfully. <a href='fixtures.php'>View Fixtures</a>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

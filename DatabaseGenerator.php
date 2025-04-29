<!DOCTYPE html>
<html>

<head>
    <title>Creating Database Table</title>
</head>

<body>

<?php
$servername = "blaineproject";
$username = "root";
$password = "";
$dbname = "St_Ronans_GAA";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Drop database if exists
$sql = "DROP DATABASE IF EXISTS $dbname;";
if ($conn->query($sql) === TRUE) {
  echo "Database created successfully<br>";
} else {
  echo "Error creating database: " . $conn->error;
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname;";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error;
}
$conn->close();

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Table creation queries
$queries = [
    "CREATE TABLE members (
        member_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(15),
        membership_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;",

    "CREATE TABLE teams (
        team_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        category ENUM('Senior', 'Junior', 'Youth', 'Social') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;",

    "CREATE TABLE team_members (
        team_member_id INT AUTO_INCREMENT PRIMARY KEY,
        member_id INT NOT NULL,
        team_id INT NOT NULL,
        position VARCHAR(50) NOT NULL,
        FOREIGN KEY (member_id) REFERENCES members(member_id),
        FOREIGN KEY (team_id) REFERENCES teams(team_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE events (
        event_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        event_date DATE NOT NULL,
        event_time TIME,
        event_type ENUM('Match', 'Training', 'Meeting', 'Social', 'Fundraiser') NOT NULL,
        location VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;",

    "CREATE TABLE roscommon_teams (
        team_id INT AUTO_INCREMENT PRIMARY KEY,
        team_name VARCHAR(100) NOT NULL,
        club_type ENUM('Football', 'Hurling', 'Both') NOT NULL,
        image_url VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )ENGINE=InnoDB;",

    "CREATE TABLE fixtures (
        fixture_id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        our_team_id INT NOT NULL,
        their_team_id INT NOT NULL,
        location ENUM('Home', 'Away', 'Neutral') NOT NULL,
        fixture_date DATE NOT NULL,
        FOREIGN KEY (our_team_id) REFERENCES teams(team_id),
        FOREIGN KEY (their_team_id)  REFERENCES roscommon_teams(team_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE results (
        result_id INT AUTO_INCREMENT PRIMARY KEY,
        fixture_id INT NOT NULL,
        our_score INT NOT NULL,
        their_score INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (fixture_id) REFERENCES fixtures(fixture_id)
    ) ENGINE=InnoDB;",

    "CREATE TABLE products (
        id INT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB;",

    "CREATE TABLE orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        address TEXT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        order_date DATETIME NOT NULL
    ) ENGINE=InnoDB;",

    "CREATE TABLE order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB;"
];

// Execute each table creation query
foreach ($queries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Insert data into tables
$insertQueries = [
    "INSERT INTO events (name, event_date, event_time, event_type, location, description) VALUES
    ('Annual Club Championship', '2023-06-25', '15:00:00', 'Match', 'Main Pitch', 'Join us for our annual club championship match. All support welcome!'),
    ('Youth Tournament', '2023-08-15', '10:00:00', 'Match', 'Training Grounds', 'Youth tournament for under 16s teams'),
    ('Senior Training Session', '2023-06-28', '18:30:00', 'Training', 'Main Pitch', 'Regular training session for senior team');",

    // Insert fixtures with correct location and team references
    "INSERT INTO fixtures (event_id, our_team_id, their_team_id, fixture_date, location) VALUES
    (1, 1, 2, '2023-06-25', 'Home'),
    (1, 3, 4, '2023-06-26', 'Away');",

    "INSERT INTO roscommon_teams (team_name, club_type, image_url) VALUES
    ('Athleague', 'Hurling', 'images/athleague.jpg'),
    ('Ballinameen', 'Football', 'images/ballinameen.jpg'),
    ('Ballinasloe', 'Football', 'images/ballinasloe.jpg'),
    ('Boyle', 'Football', 'images/boyle.jpg'),
    ('Castlerea St Kevins', 'Football', 'images/castlerea.jpg'),
    ('Clann na nGael', 'Football', 'images/clann_na_ngael.jpg'),
    ('Creagh', 'Football', 'images/creagh.jpg'),
    ('Eire Óg', 'Football', 'images/eire_og.jpg'),
    ('Elphin', 'Football', 'images/elphin.jpg'),
    ('Faithleach’s', 'Football', 'images/faithleachs.jpg'),
    ('Fuerty', 'Football', 'images/fuerty.jpg'),
    ('Kilbride', 'Football', 'images/kilbride.jpg'),
    ('Kilglass Gaels', 'Football', 'images/kilglass_gaels.jpg'),
    ('Kilmore', 'Football', 'images/kilmore.jpg'),
    ('Michael Glaveys', 'Football', 'images/michael_glaveys.jpg'),
    ('Oran', 'Both', 'images/oran.jpg'),
    ('Padraig Pearses', 'Both', 'images/padraig_pearses.jpg'),
    ('Roscommon Gaels', 'Both', 'images/roscommon_gaels.jpg'),
    ('Shannon Gaels', 'Football', 'images/shannon_gaels.jpg'),
    ('St Aidans', 'Football', 'images/st_aidans.jpg'),
    ('St Barrys', 'Football', 'images/st_barrys.jpg'),
    ('St Brigids', 'Football', 'images/st_brigids.jpg'),
    ('St Croans', 'Football', 'images/st_croans.jpg'),
    ('St Dominics', 'Both', 'images/st_dominics.jpg'),
    ('St Faithleachs', 'Football', 'images/st_faithleachs.jpg'),
    ('St Josephs', 'Football', 'images/st_josephs.jpg'),
    ('St Michaels', 'Football', 'images/st_michaels.jpg'),
    ('St Ronans', 'Football', 'images/st_ronans.jpg'),
    ('Strokestown', 'Football', 'images/strokestown.jpg'),
    ('Tulsk', 'Football', 'images/tulsk.jpg'),
    ('Western Geals', 'Football', 'images/WesternGaels.jpg');",

    "INSERT INTO results (fixture_id, our_score, their_score) VALUES
    (1, 3, 2),
    (2, 1, 1);",

    "INSERT INTO products (id, name, price, image) VALUES
(1, 'Club Jersey', 50.00, 'st_ronans_gaa_club_roy_whi_jersey.jpg'),
(2, 'Club Sweatshirt', 50.00, 'st_ronans_sweatshirt.jpg'),
(3, 'Club Hoodie', 50.00, 'st_ronans_hoodie.jpg'),
(4, 'Club Shorts', 40.00, 'osprey-woven-shorts.jpg'),
(5, 'Club Bottoms', 50.00, 'st_ronans_bottoms.jpg');"
];

// Execute each insert query
foreach ($insertQueries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully<br>";
    } else {
        echo "Error inserting data: " . $conn->error . "<br>";
    }
}

$conn->close();
?>

</body>
</html>

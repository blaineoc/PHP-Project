<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - St Ronan's GAA Club</title>
    <link rel="icon" type="image/png" href="images/st_ronans.png" />
    <link rel="stylesheet" href="css/styles.css">
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
            <li><a href="shoppingcart.php">Cart</a></li>
        </ul>
    </nav>

    <div class="contact-container">
        <h2>Contact Us</h2>
        <form class="contact-form" action="contact_form.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>

            <button type="submit" class="btn-submit">Send Message</button>
        </form>
    </div>
</body>

</html>

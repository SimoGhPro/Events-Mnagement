<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  // Redirect to the login page if not logged in
  header("Location: login.php");
  exit();
}
include 'utils/functions.php';
if (!is_admin()) {
  header("Location: accessDenied.php");
  exit();
}

// Include the database connection file
include 'utils/db_connection.php';

// Get the organizer ID from the session
$organizerID = $_SESSION['user_id'];

// Retrieve events for the logged-in user from the database
$selectQuery = "SELECT * FROM events WHERE organizer_id = '$organizerID'";
$result = mysqli_query($conn, $selectQuery);

$organizerQuery = "SELECT * FROM users WHERE user_id = '$organizerID'";
$result2 = mysqli_query($conn, $organizerQuery);

if (!$result || !$result2) {
  die("Error executing the query: " . mysqli_error($conn));
}

// Fetch and display events
$events = mysqli_fetch_all($result, MYSQLI_ASSOC);
$organizer = mysqli_fetch_assoc($result2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events Dashboard</title>
  <link rel="icon" href="images/event.png" type="image/png">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    main {
      max-width: 800px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h3 {
      color: #551a8b;
    }

    #profile-img {
      border-radius: 50%;
      margin-right: 10px;
    }

    .event-row {
      border-bottom: 1px solid #ccc;
      padding: 20px 0;
      display: flex;
      justify-content: space-between;
      align-items: center; /* Align items vertically in the center */
    }

    .details a {
      margin-right: 10px;
      padding: 8px 16px;
      background: linear-gradient(0deg, rgba(242, 164, 223, 0.9948354341736695) 18%, rgba(144, 57, 210, 1) 90%);
      color: #fff;
      text-decoration: none;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button {
      padding: 8px 16px;
      margin-right: 10px;
      background-color: #551a8b;
      color: #fff;
      text-decoration: none;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .details a:hover,
    .event-actions button:hover {
      background-color: #7d26cd;
    }

    span {
      width: 100px;
    }

    
    .profile {
      display: flex;
      align-items: center; /* Align items vertically */ 
    }

    #profile-img, .profile p {
      margin-left: 25%;
      margin-right: 25%;
    }

    .profile p{
      font-weight: bold;
    }

    #create-event {
      display: block;
      width: fit-content;
      margin: 20px auto;
      padding: 10px 20px;
      background-color: #551a8b;
      color: #fff;
      text-decoration: none;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    #create-event:hover {
      background-color: #7d26cd;
    }

    @media (max-width: 600px) {
      main {
        padding: 10px;
      }
    }
  </style>
</head>

<body>
  <?php require 'layout/header.php'; ?>
  <main>
    <div class="profile">
      <img id="profile-img" src="<?php echo $organizer['user_img']; ?>" alt="profile-img" width="50" height="50">
      <p>
        <?php echo $_SESSION['username']; ?>
      </p>
    </div>
    <div>
      <?php
      if (empty($events)) {
        echo "<p>No events found.</p>";
      } else {
        foreach ($events as $event) {
          echo
            "<div class='event-row'>
              <span><p>{$event['event_name']}</p></span>
              <div class='details'>
              <a href='event.php?id={$event['event_id']}'>View</a>
              <a href='eventDashboard.php?id={$event['event_id']}'>Info</a>
              <a href='editEvent.php?id={$event['event_id']}'>Edit</a>
              </div>
              <form id=\"deleteEventForm{$event['event_id']}\" action='deleteEvent.php' method='POST'>
                <button type='button' onclick='confirmDelete(\"deleteEventForm{$event['event_id']}\")'>Delete</button>
              </form>
          </div>";
        }
      }
      ?>
    </div>
    <a id="create-event" href="createEvent.php">Create New Event</a>
  </main>
  <?php require 'layout/footer.php'; ?>
</body>

<script>
  function confirmDelete(formId) {
    var result = confirm("Are you sure you want to delete this event?");
    if (result) document.getElementById(formId).submit();
  }
</script>

</html>

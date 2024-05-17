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

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
  // Get the event ID from the URL
  $eventID = $_GET['id'];

  // Retrieve the event details from the database
  $selectEventQuery = "SELECT * FROM events WHERE event_id = '$eventID'";
  $eventResult = mysqli_query($conn, $selectEventQuery);

  if (!$eventResult) {
    die("Error executing the event query: " . mysqli_error($conn));
  }

  // Fetch event details
  $event = mysqli_fetch_assoc($eventResult);

  // Retrieve the list of registered users for the event
  $selectUsersQuery = "
    SELECT users.user_id, users.username, users.email, users.user_img
    FROM users
    JOIN registrations ON users.user_id = registrations.user_id
    WHERE registrations.event_id = '$eventID'
  ";
  $usersResult = mysqli_query($conn, $selectUsersQuery);

  if (!$usersResult) {
    die("Error executing the users query: " . mysqli_error($conn));
  }

  // Fetch and display event details and registered users
  $users = mysqli_fetch_all($usersResult, MYSQLI_ASSOC);
} else {
  // Redirect to the index page if 'id' parameter is not set
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Dashboard</title>
  <link rel="stylesheet" type="text/css" href="styles/globals.css">
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

    h1 {
      color: #551a8b;
      margin-bottom: 10px;
    }

    h2 {
      color: #551a8b;
      margin-top: 20px;
    }

    h3 {
      color: #551a8b;
      margin-bottom: 5px;
    }

    p {
      margin: 5px 0;
    }

    img {
      margin-right: 10px;
    }

    .user-details {
      display: flex;
      align-items: center;
    }

    .user-details div {
      margin-right: 20px;
    }

    #map {
      height: 300px;
      margin-top: 20px;
    }

    .chart-container {
      margin-top: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
      padding: 20px;
      background-color: #f9f9f9;
    }

    @media (max-width: 600px) {
      main {
        padding: 10px;
      }
    }
  </style>
  <?php require 'utils/config.php'; ?>
  <link rel="icon" href="images/event.png" type="image/png">
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsApiKey; ?>&callback=initMap"></script>
  <script>
    var map;
    var marker;
    function initMap() {
      // Initialize the map
      map = new google.maps.Map(document.getElementById('map'), {
        center: {
          lat: <?php echo $event['event_latitude']; ?>,
          lng: <?php echo $event['event_longitude']; ?>
        },
        zoom: 15 // You can adjust the zoom level as needed
      });
      // Add a marker for the event's location
      marker = new google.maps.Marker({
        position: {
          lat: <?php echo $event['event_latitude']; ?>,
          lng: <?php echo $event['event_longitude']; ?>
        },
        map: map,
        title: 'Event Location'
      });
    }
  </script>
</head>

<body>
  <?php require 'layout/header.php'; ?>
  <main>
    <div>
      <div>
        <img src="<?php echo $event['event_img']; ?>" alt="event-img" width="250">
      </div>
      <h1><?php echo $event['event_name']; ?></h1>
    </div>
    <div>
      <p><strong>Category:</strong> <?php echo $event['event_type']; ?></p>
      <p><strong>Date:</strong> <?php echo $event['event_date']; ?></p>
    </div>
    <p><strong>Details:</strong> <?php echo $event['event_details']; ?></p>
    <?php
    if ($event['event_latitude'] != 0 && $event['event_longitude'] != 0) {
      echo "<div id='map'></div>";
    } else {
      echo "<p>No Location in this event.</p>";
    }
    ?>


    <div class="chart-container">
      <canvas id="userChart"></canvas>
    </div>
  </main>
  <?php require 'layout/footer.php'; ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
  <script>
    // Get the number of registered users for the event
    var registeredUsers = <?php echo count($users); ?>;
    
    // Get the context of the canvas element we want to select
    var ctx = document.getElementById('userChart').getContext('2d');
    
    // Create the chart
    var userChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Registered Users'],
        datasets: [{
          label: 'Number of Users',
          data: [registeredUsers],
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>

</html>

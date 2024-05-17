<?php
// Include the database connection file
include 'utils/db_connection.php';

// Retrieve all event types
$selectEventTypeQuery = "SELECT DISTINCT event_type FROM events";
$resultEventType = mysqli_query($conn, $selectEventTypeQuery);

if (!$resultEventType) {
  die("Error executing the query: " . mysqli_error($conn));
}

// Fetch event types
$eventTypes = mysqli_fetch_all($resultEventType, MYSQLI_ASSOC);

// Retrieve all events from the database
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterEventType = isset($_GET['event_type']) ? $_GET['event_type'] : '';

$selectQuery =
  "SELECT * FROM events
    WHERE (event_name LIKE '%$search%'
    OR event_details LIKE '%$search%')"
  . ($filterEventType ? "AND event_type = '$filterEventType'" : "")
  . " ORDER BY event_date ASC";
$result = mysqli_query($conn, $selectQuery);

if (!$result) {
  die("Error executing the query: " . mysqli_error($conn));
}

// Fetch and display events
$events = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
  <script type='text/javascript' src='script/jquery.min.js'></script>
  <script type='text/javascript' src='script/jquery.mobile.customized.min.js'></script>
  <script type='text/javascript' src='script/jquery.easing.1.3.js'></script>
  <script type='text/javascript' src='script/camera.min.js'></script>
  <style>
    body {
  background-color: #f2e6ff; /* Light purple background */
  font-family: Arial, sans-serif;
  color: #333; /* Text color */
}

main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.event-card-ctr {
  display: flex;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  grid-gap: 20px;
}

.event-card {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}


.event-card h3 {
  font-size: 18px;
  margin: 10px 0;
}

.event-card p {
  font-size: 14px;
  color: #777;
}

.event-card img{
  width: 250px;
  height: 250px;
}

/* Styling the 'View more details' button */
.event-card a {
  display: inline-block;
  background-color: #6a0dad; /* Purple button color */
  color: #fff;
  text-decoration: none;
  padding: 10px 20px;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.event-card a:hover {
  background-color: #49077e; /* Darker shade of purple on hover */
}

.event-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}


/* Innovative and attractive search banner */
form {
  background-color: #9b59b6; /* Purple banner color */
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 20px;
}

input[type="text"] {
  width: 70%;
  padding: 10px;
  border: none;
  border-radius: 5px;
}

button[type="submit"] {
  background-color: #6a0dad; /* Purple button color */
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button[type="submit"]:hover {
  background-color: #49077e; /* Darker shade of purple on hover */
}

  .info {
    display: flex; /* Stretch children to full height */
    flex: 1; /* Take remaining space */
    padding: 20px;
    background-color: #fff; /* White background */
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-right: 20px; /* Add some spacing */
  }

  .container{
    width: 50%;
  }

  .camera-wrap {
    width: 20%;
    margin-left: auto;
    margin-top: 30px;
  }
  </style>
    <link rel="icon" href="images/event.png" type="image/png">
</head>

<body>
  <?php require 'layout/header.php'; ?>
  <main>
    <form method="get" action="">
      <input type="text" name="search" placeholder="Search for events..."
        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

      <label for="event_type">Categorie:</label>
      <select id="event_type" name="event_type">
        <option value="">All</option>
        <?php foreach ($eventTypes as $eventType): ?>
          <option value="<?php echo $eventType['event_type']; ?>">
            <?php echo $eventType['event_type']; ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit">Submit</button>
    </form>

    <div id="event-card-ctr">
      <?php
      if (empty($events)) {
        echo "<p>No events found.</p>";
      } else {
        // Counter for controlling the number of cards per row
        $count = 0;

        foreach ($events as $event) {
          // Start a new row after every 3rd card
          if ($count % 3 == 0) {
            echo "<div class='event-row'>";
          }

          echo "<div class='event-card'>
                  <div>
                    <div>
                      <img src='{$event['event_img']}' alt='img'>
                    </div>
                    <h3>{$event['event_name']}</h3>
                    <p>Date: {$event['event_date']}</p>
                  </div>
                  <a href='event.php?id={$event['event_id']}' class='purple-button'>
                    View More Details
                  </a>
                </div>";

          // Close the row after every 3rd card
          if (($count + 1) % 3 == 0 || ($count + 1) == count($events)) {
            echo "</div>";
          }

          $count++;
        }
      }
      ?>
    </div>
<section class="info">
<div class="container">
<?php
// Retrieve data for the line graph (number of visitors per day)
$selectVisitorsQuery = "SELECT DATE(registration_date) AS login_date, COUNT(*) AS num_visitors FROM registrations GROUP BY login_date";
$resultVisitors = mysqli_query($conn, $selectVisitorsQuery);

if (!$resultVisitors) {
  die("Error executing the query: " . mysqli_error($conn));
}

// Initialize arrays to store x and y values
$xValues = [];
$yValues = [];

// Fetch and format the data for the line graph
while ($row = mysqli_fetch_assoc($resultVisitors)) {
  $xValues[] = $row['login_date']; // Assuming login_date is in the format you want to display
  $yValues[] = $row['num_visitors'];
}
?>
            <canvas id="myChart" style="width:100%;max-width:700px"></canvas>
        </div>
<div class="camera-wrap">
            <div class="fluid_container">
                <div class="camera_wrap camera_violet_skin" id="MonSlider">
                    <?php foreach ($events as $event): ?>
                        <div data-src="<?php echo $event['event_img']; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
  </section>
  </main>
  <?php require 'layout/footer.php'; ?>
</body>


<script>
  // Wait for the DOM to be fully loaded before initializing Chart.js
  document.addEventListener("DOMContentLoaded", function() {
    const xValues = <?php echo json_encode($xValues); ?>;
    const yValues = <?php echo json_encode($yValues); ?>;

    new Chart("myChart", {
      type: "line",
      data: {
        labels: xValues,
        datasets: [{
          label: "Number of Visitors",
          backgroundColor: "rgba(0,0,255,0.1)",
          borderColor: "rgba(0,0,255,1.0)",
          data: yValues
        }]
      },
      options: {
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    });
  });
</script>

<script>
    jQuery(function(){
        jQuery("#MonSlider").camera({
            portrait: false, // Display images centered within slides
            time: 2000, // Time between each slide
            transPeriod: 1000, // Transition duration
            loader: 'none',
            barPosition: 'bottom',
            pagination: false,
            thumbnails: false
        });
    });
</script>

<script>
  // Set default value to event_type using JavaScript
  document.getElementById("event_type").value = "<?php echo $filterEventType; ?>";
</script>

</html>
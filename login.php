<?php
// Include the database connection file
include 'utils/db_connection.php';

// Start session to persist user login
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get user input from the form
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Validate user input (you can add more validation as needed)
  if (empty($username) || empty($password)) {
    $error_message = "Username and password are required.";
  } else {
    // Sanitize user input to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Query the database to check user credentials
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result) {
      // Check if a matching user is found
      if (mysqli_num_rows($result) == 1) {
        // User is authenticated, store user data in session
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['user_role'];

        // Redirect to a dashboard or home page
        if ($_SESSION['user_role'] == "admin") {
          header("Location: viewEvents.php");
        } else if ($_SESSION['user_role'] == "user") {
          header("Location: index.php");
        }
        exit();
      } else {
        $error_message = "Invalid username or password.";
      }
    } else {
      $error_message = "Error executing the query: " . mysqli_error($conn);
    }
  }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
  @import url("layout.css");
  body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-image: url('images/eventback.jpg');
      background-size: cover;
      background-position: center;
    }

    main {
      max-width: 400px;
      margin: 50px auto;
      background-color: #7b68ee; /* Purple background */
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #fff; /* White text */
    }

    form {
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 10px;
      color: #fff; /* White text */
    }

    input[type="text"],
    input[type="password"] {
      width: 50%;
      padding: 10px;
      margin-bottom: 20px;
      border: none;
      border-radius: 5px;
    }

    button[type="submit"] {
      background-color: #551a8b; /* Dark purple button */
      color: #fff; /* White text */
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #7d26cd; /* Lighter purple on hover */
    }

    p {
      text-align: center;
      margin-top: 20px;
      color: #fff; /* White text */
    }

    a {
      color: #fff; /* White text */
      text-decoration: none;
      border-bottom: 1px dashed #fff; /* Dashed underline effect */
      transition: border-bottom 0.3s ease;
    }

    a:hover {
      border-bottom: 1px dashed transparent; /* Hide underline on hover */
    }
  </style>
    <link rel="icon" href="images/event.png" type="image/png">
</head>

<body>
  <main>
    <h2>Welcome to ENSAK Events</h2>
    <?php
    // Display error message if any
    if (isset($error_message)) {
      echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form method="post" action="">
      <label for="username">Username</label>
      <input type="text" name="username" required>
      <label for="password">Password</label>
      <input type="password" name="password" required><br>
      <button type="submit">Sign In</button>
    </form>
    <p>Don't have an account? <a href="register.php">Sign Up</a></p>
  </main>
  <?php require 'layout/footer.php'; ?>
</body>

</html>
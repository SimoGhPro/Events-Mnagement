<?php
include 'utils/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $email = $_POST['email'];

  // Default image path if the user does not provide one
  $defaultImage = 'images/inconnu.png';

  if (empty($username) || empty($password)) {
    $error_message = "Username and password are required.";
  } else {
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);
    $email = mysqli_real_escape_string($conn, $email);

    // Check if the username is already taken
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
      $error_message = "Username is already taken.";
    } else {
      // Check if user uploaded an image
      if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] == 0) {
        $target_dir = 'images/';  // Set your target directory
        $target_file = $target_dir . basename($_FILES['user_img']['name']);

        // Check file size (2MB limit)
        if ($_FILES['user_img']['size'] > 2 * 1024 * 1024) {
          $error_message = "File size exceeds the limit of 2MB.";
        } else {
          move_uploaded_file($_FILES['user_img']['tmp_name'], $target_file);
          $userImage = $target_file;
        }
      } else {
        // Use the default image
        $userImage = $defaultImage;
      }

      // Insert the new user into the database
      $insert_query =
        "INSERT INTO users (username, email, password, user_img) 
        VALUES ('$username', '$email', '$password', '$userImage')";
      $insert_result = mysqli_query($conn, $insert_query);

      if ($insert_result) {
        // Registration successful, redirect to login page
        header("Location: login.php");
        exit();
      } else {
        $error_message = "Error executing the query: " . mysqli_error($conn);
      }
    }
  }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration</title>
  <style>
    /* Global styles */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(0deg, rgba(242,164,223,0.9948354341736695) 18%, rgba(144,57,210,1) 90%);
    }

    main {
      max-width: 400px;
      margin: 50px auto;
      background-color: #fff; /* White background */
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #551a8b; /* Dark purple text */
    }

    form {
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 10px;
      color: #551a8b; /* Dark purple text */
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
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
      color: #551a8b; /* Dark purple text */
    }

    a {
      color: #551a8b; /* Dark purple text */
      text-decoration: none;
      border-bottom: 1px dashed #551a8b; /* Dashed underline effect */
      transition: border-bottom 0.3s ease;
    }

    a:hover {
      border-bottom: 1px dashed transparent; /* Hide underline on hover */
    }
  </style>
    <link rel="icon" href="images/event.png" type="image/png">
</head>

<body>
  <?php require 'layout/header.php'; ?>
  <main>
    <h2>Registration From</h2>
    <?php
    if (isset($error_message)) {
      echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form method="post" action="" enctype="multipart/form-data">
      <label for="username">Username:</label>
      <input type="text" name="username" required>
      <label for="email">Email:</label>
      <input type="email" name="email" required>
      <label for="password">Password:</label>
      <input type="password" name="password" required>
      <label for="user_img">Profile Image:</label>
      <!-- max img size is 2Mo -->
      <input type="file" name="user_img" accept="image/*" maxlength="2000000">
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Log In</a></p>
  </main>
  <?php require 'layout/footer.php'; ?>
</body>

</html>
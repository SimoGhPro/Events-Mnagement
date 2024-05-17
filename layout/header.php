<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    /* Header styles */
    header {
      background-color: #551a8b; /* Dark purple background */
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .logo img {
      width: 200px;
    }

    .nav-links {
      display: flex;
      align-items: center;
    }

    .nav-links a {
      color: #fff; /* White text */
      margin-right: 20px;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .nav-links a:hover {
      color: #7d26cd; /* Lighter purple on hover */
    }

    @media (max-width: 768px) {
      nav {
        flex-direction: column;
        align-items: flex-start;
      }

      .nav-links {
        margin-top: 10px;
      }

      .nav-links a {
        margin-right: 0;
        margin-bottom: 10px;
      }
    }
  </style>
  <header>
    <nav>
      <?php
      require_once 'utils/functions.php';
      echo '<a href="index.php" class="logo"><img src="images/logo.png"></a>';
      ?>
      <div class="nav-links">
        <?php
        if (is_logged_in() && is_admin()) {
          require_once 'utils/functions.php';
          echo '<a href="viewEvents.php">My Events</a>';
          echo '<a href="logout.php">Logout</a>';
        } else if (is_logged_in()) {
          echo '<a href="logout.php">Logout</a>';
        } else {
          echo '<a href="login.php">Login</a>';
        }
        ?>
      </div>
    </nav>
  </header>
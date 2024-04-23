<?php
session_start(); // Start session to store password validation status

// Check if the password is submitted
if(isset($_POST['password'])){
    include 'conn.php';

    $password = $_POST['password'];

    // Fetch the password from the database
    $sql = "SELECT timein_key FROM admin";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the first row (assuming there is only one admin row)
        $row = $result->fetch_assoc();
        $timeinKeyFromDB = $row["timein_key"];

        // Check if the provided password matches the password from the database
        if ($password === $timeinKeyFromDB) {
            $_SESSION['authenticated'] = true; // Store authentication status in session
            header("Location: http://localhost/attendtrack/manual_tito.php"); // Redirect to the time in/time out page
            exit();
        } else {
            $errorMessage = 'Wrong password'; // Set error message
        }
    } else {
        $errorMessage = 'No admin found'; // Set error message
    }
}

// Redirect to the homepage after 60 seconds
header("Refresh: 60; URL=http://localhost/attendtrack/");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Entry</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
  /* Centering the form */
  body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }
  form {
    text-align: center;
    font-size: 20px;
  }
  input[type="password"] {
    padding: 10px;
    font-size: 18px;
    margin-bottom: 10px;
  }
</style>
</head>
<body>

<form id="passwordForm" method="POST">
  <label for="password">Enter Password:</label><br>
  <input type="password" id="password" name="password" autofocus><br>
  <input type="submit" value="Submit">
  <p id="errorMessage" style="color: red;"><?php echo isset($errorMessage) ? $errorMessage : ''; ?></p>
</form>

<script>
  // Focus on the password field every 7 seconds
  setInterval(function() {
    $('#password').focus();
  }, 7000);

  // Redirect to the homepage after 60 seconds
  setTimeout(function() {
    window.location.href = 'http://localhost/attendtrack/';
  }, 15000);
</script>

</body>
</html>

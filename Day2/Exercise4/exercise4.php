<html>
  <head>
    <meta http-equiv="refresh" content="30; url=<?php echo
      $_SERVER['PHP_SELF']; ?>">
  </head>
  <body>
    <h2>Welcome to Block 16 Weather Station</h2>
    <?php
      $fileStr = file_get_contents('http://scm.ulster.ac.uk/weather/Realtime.txt');
      $bits = explode(" ", $fileStr);
      echo "<p>Date: $bits[0]</p>";
      echo "<p>Time: $bits[1]</p>";
      $temperature = $bits[2];
      echo "<p>Outside Temp: $temperature $bits[14] <progress value=\"$temperature\" max=\"40\"></progress></p>";
      $windSpeed = $bits[6];
      echo "<p>Wind Speed: $windSpeed $bits[13] <progress value=\"$windSpeed\" max=\"10\"></progress></p>";
    ?>
  </body>
</html>
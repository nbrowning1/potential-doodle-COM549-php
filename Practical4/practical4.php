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

    $windSpeedUnit = $bits[13];
    $temperatureUnit = $bits[14];
    
    $date = $bits[0];
    $time = $bits[1];
    $temperature = $bits[2];
    $humidity = $bits[3];
    $dewPoint = $bits[4];
    $avgWindSpeed = $bits[5];
    $latestWindSpeed = $bits[6];
    $windBearing = $bits[7];
    $currentRainRate = $bits[8];
    $rainToday = $bits[9];
    $seaLevelPressure = $bits[10];
    $windDirection = $bits[11];
    $beaufortWindSpeed = $bits[12];

    echo "<p>Date: $date</p>";
    echo "<p>Time: $time</p>";
    echo "<p>Outside Temp: $temperature $temperatureUnit <progress value=\"$temperature\" max=\"40\"></progress></p>";
    echo "<p>Latest Wind Speed: $latestWindSpeed $windSpeedUnit <progress value=\"$latestWindSpeed\" max=\"10\"></progress></p>";
    
    ?>
  </body>
</html>

0
1
2

6

13
14
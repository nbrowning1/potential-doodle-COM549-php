<html>
  <head>
    <meta http-equiv="refresh" content="30; url=<?php echo
      $_SERVER['PHP_SELF']; ?>">
    
    <style>
    *   {font-family: Arial, Helvetica, sans-serif;}
    </style>
  </head>
  <body>
    <h2>Welcome to Block 16 Weather Station</h2>
    <?php
    
    $fileStr = file_get_contents('http://scm.ulster.ac.uk/weather/Realtime.txt');
    $bits = explode(" ", $fileStr);
    
    // Common unit values
    $windSpeedUnit = $bits[13];
    $temperatureUnit = $bits[14];
    $pressureUnit = $bits[15];
    $rainUnit = $bits[16];
    $cloudBaseUnit = $bits[53];

    outputStatistic("Date", 0, null);
    outputStatistic("Time", 1, null);
    outputStatisticWithProgress("Outside temperature", 2, $temperatureUnit, 40);
    outputStatistic("Relative humidity", 3, '%');
    outputStatistic("Dewpoint", 4, $temperatureUnit);
    outputStatistic("Wind speed (average)", 5, $windSpeedUnit);
    outputStatistic("Latest wind speed reading", 6, $windSpeedUnit);
    outputStatistic("Wind bearing (degrees)", 7, null);
    outputStatistic("Current rain rate (per hour)", 8, $rainUnit);
    outputStatistic("Rain today", 9, $rainUnit);
    outputStatistic("Barometer (sea level pressure)", 10, $pressureUnit);
    outputStatistic("Current wind direction", 11, null);
    outputStatistic("Wind speed (beaufort)", 12, null);
    outputStatistic("Wind run (today)", 17, null);
    outputStatistic("Pressure trend value", 18, $pressureUnit);
    outputStatistic("Monthly rainfall", 19, $rainUnit);
    outputStatistic("Yearly rainfall", 20, $rainUnit);
    outputStatistic("Yesterday's rainfall", 21, $rainUnit);
    outputStatistic("Inside temperature", 22, $temperatureUnit);
    outputStatistic("Inside humidity", 23, '%');
    outputStatistic("Wind chill", 24, $temperatureUnit);
    outputStatistic("Temperature trend value", 25, $temperatureUnit);
    
    echo '<hr>';
    
    outputStatistic("Today's high temperature", 26, $temperatureUnit);
    outputStatistic("Time of today's high temperature", 27, null);
    outputStatistic("Today's low temperature", 28, $temperatureUnit);
    outputStatistic("Time of today's low temperature", 29, null);
    outputStatistic("Today's high wind speed", 30, $windSpeedUnit);
    outputStatistic("Time of today's high wind speed", 31, null);
    outputStatistic("Today's high wind gust", 32, null);
    outputStatistic("Time of today's high wind gust", 33, null);
    outputStatistic("Today's high pressure", 34, $pressureUnit);
    outputStatistic("Time of today's high pressure", 35, null);
    outputStatistic("Today's low pressure", 36, $pressureUnit);
    outputStatistic("Time of today's low pressure", 37, null);
    
    echo '<hr>';
    
    outputStatistic("Cumulus Versions", 38, null);
    outputStatistic("Cumulus build number", 39, null);
    outputStatistic("10-minute high gust", 40, null);
    outputStatistic("Heat index", 41, $temperatureUnit);
    outputStatistic("Humidex", 42, null);
    outputStatistic("UV index", 43, null);
    outputStatistic("Evapotranspiration today", 44, $rainUnit);
    outputStatistic("Solar radiation", 45, "W/m2");
    outputStatistic("10-minute average wind bearing", 46, "degrees");  
    outputStatistic("Rainfall last hour", 47, $rainUnit);
    outputStatistic("Number of the current (Zambretti) forecast", 48, null);
    outputFlagStatistic("Station is currently in daylight", 49, null);
    outputFlagStatistic("Station has lost contact with its remote sensors", 50, null);
    outputStatistic("Average wind direction", 51, null);
    outputStatistic("Cloud base", 52, $cloudBaseUnit);
    outputStatistic("Apparent temperature", 54, $temperatureUnit);
    outputStatistic("Sunshine hours so far today", 55, null);
    outputStatistic("Current theoretical max solar radiaton", 56, null);
    outputFlagStatistic("The sun is shining", 57, null);
    
    // Outputs a regular statistic - a label, the array index to derive the value and units
    function outputStatistic($label, $arrayIndex, $unit) {
      global $bits;
      echo "<p><b>$label</b>: $bits[$arrayIndex] $unit</p>";
    }
    
    /* Outputs a flag statistic - same as outputStatistic() but will evaluate 'false' or 'true'
        for the value to make it more human-readable */
    function outputFlagStatistic($label, $arrayIndex, $unit) {
      global $bits;
      $value = $bits[$arrayIndex];
      $value = $value == 0 ? 'false' : 'true';
      echo "<p><b>$label</b>: $value $unit</p>";
    }
    
    /* Outputs a progress statistic - same as outputStatistic() but will also display a progress bar
        using $maxVal variable to define the upper limit of the progress bar */
    function outputStatisticWithProgress($label, $arrayIndex, $unit, $maxVal) {
      global $bits;
      $value = $bits[$arrayIndex];
      echo "<p><b>$label</b>: $value $unit <progress value=\"$value\" max=\"$maxVal\"></progress></p>";
    }
    
    ?>
  </body>
</html>
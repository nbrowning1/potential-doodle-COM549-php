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
    
    $windSpeedUnit = $bits[13];
    $temperatureUnit = $bits[14];
    $pressureUnit = $bits[15];
    $rainUnit = $bits[16];
    
    $windRun = $bits[17];
    $pressureTrend = $bits[18];
    $monthlyRainfall = $bits[19];
    $yearlyRainfall = $bits[20];
    $yesterdayRainfall = $bits[21];
    $insideTemperature = $bits[22];
    $insideHumidity = $bits[23];
    $windChill = $bits[24];
    $temperatureTrendValue = $bits[25];
    $todayHighTemperature = $bits[26];
    $todayHighTemperatureTime = $bits[27];
    $todayLowTemperature = $bits[28];
    $todayLowTemperatureTime = $bits[29];
    $todayHighWindSpeed = $bits[30];
    $todayHighWindSpeedTime = $bits[31];
    $todayHighWindGust = $bits[32];
    $todayHighWindGustTime = $bits[33];
    $todayHighPressure = $bits[34];
    $todayHighPressureTime = $bits[35];
    $todayLowPressure = $bits[36];
    $todayLowPressureTime = $bits[37];
    $cumulusVersions = $bits[38];
    $cumulusBuildNumber = $bits[39];
    $tenMinuteHighGust = $bits[40];
    $heatIndex = $bits[41];
    $humidex = $bits[42];
    $uvIndex = $bits[43];
    $evapotranspirationToday = $bits[44];
    $solarRadiation = $bits[45];
    $tenMinuteAvgWindBearing = $bits[46];
    $rainfallLastHour = $bits[47];
    $currentZambrettiForecast = $bits[48];
    $stationInDaylightFlag = $bits[49];
    $stationLostContactWithSensors = $bits[50];
    $avgWindDirection = $bits[51];
    $cloudBase = $bits[52];
    $cloudBaseUnits = $bits[53];
    $apparentTemperature = $bits[54];
    $sunshineHoursToday = $bits[55];
    $theoreticalMaxSolarRadiation = $bits[56];
    $isItSunny = $bits[57];

    outputStatistic("Date", $date, null);
    outputStatistic("Time", $time, null);
    outputStatisticWithProgress("Outside temperature", $temperature, $temperatureUnit, 40);
    outputStatistic("Relative humidity", $humidity, null);
    outputStatistic("Dewpoint", $dewPoint, null);
    outputStatistic("Wind speed (average)", $avgWindSpeed, null);
    outputStatistic("Latest wind speed reading", $latestWindSpeed, null);
    outputStatistic("Wind bearing (degrees)", $windBearing, null);
    outputStatistic("Current rain rate (per hour)", $currentRainRate, null);
    outputStatistic("Rain today", $rainToday, null);
    outputStatistic("Barometer (sea level pressure)", $seaLevelPressure, null);
    outputStatistic("Current wind direction", $windDirection, null);
    outputStatistic("Wind speed (beaufort)", $beaufortWindSpeed, null);
    outputStatistic("Wind run (today)", $windRun, null);
    outputStatistic("Pressure trend value", $pressureTrend, null);
    outputStatistic("Monthly rainfall", $monthlyRainfall, null);
    outputStatistic("Yearly rainfall", $yearlyRainfall, null);
    outputStatistic("Yesterday's rainfall", $yesterdayRainfall, null);
    outputStatistic("Inside temperature", $insideTemperature, null);
    outputStatistic("Inside humidity", $insideHumidity, null);
    outputStatistic("Wind chill", $windChill, null);
    outputStatistic("Temperature trend value", $temperatureTrendValue, null);
    
    echo '<hr>';
    
    outputStatistic("Today's high temperature", $todayHighTemperature, null);
    outputStatistic("Time of today's high temperature", $todayHighTemperatureTime, null);
    outputStatistic("Today's low temperature", $todayLowTemperature, null);
    outputStatistic("Time of today's low temperature", $todayLowTemperatureTime, null);
    outputStatistic("Today's high wind speed", $todayHighWindSpeed, null);
    outputStatistic("Time of today's high wind speed", $todayHighWindSpeedTime, null);
    outputStatistic("Today's high wind gust", $todayHighWindGust, null);
    outputStatistic("Time of today's high wind gust", $todayHighWindGustTime, null);
    outputStatistic("Today's high pressure", $todayHighPressure, null);
    outputStatistic("Time of today's high pressure", $todayHighPressureTime, null);
    outputStatistic("Today's low pressure", $todayLowPressure, null);
    outputStatistic("Time of today's low pressure", $todayLowPressureTime, null);
    
    echo '<hr>';
    
    outputStatistic("Cumulus Versions", $cumulusVersions, null);
    outputStatistic("Cumulus build number", $cumulusBuildNumber, null);
    outputStatistic("10-minute high gust", $tenMinuteHighGust, null);
    outputStatistic("Heat index", $heatIndex, null);
    outputStatistic("Humidex", $humidex, null);
    outputStatistic("UV index", $uvIndex, null);
    outputStatistic("Evapotranspiration today", $evapotranspirationToday, null);
    outputStatistic("Solar radiation", $solarRadiation, "W/m2");
    outputStatistic("10-minute average wind bearing", $tenMinuteAvgWindBearing, "degrees");  
    outputStatistic("Rainfall last hour", $rainfallLastHour, null);
    outputStatistic("Number of the current (Zambretti) forecast", $currentZambrettiForecast, null);
    outputFlagStatistic("Station is currently in daylight", $stationInDaylightFlag, null);
    outputFlagStatistic("Station has lost contact with its remote sensors", $stationLostContactWithSensors, null);
    outputStatistic("Average wind direction", $avgWindDirection, null);
    outputStatistic("Cloud base", $cloudBase, null);
    outputStatistic("Cloud base units", $cloudBaseUnits, null);
    outputStatistic("Apparent temperature", $apparentTemperature, null);
    outputStatistic("Sunshine hours so far today", $sunshineHoursToday, null);
    outputStatistic("Current theoretical max solar radiaton", $theoreticalMaxSolarRadiation, null);
    outputFlagStatistic("The sun is shining", $isItSunny, null);
    
    function outputStatistic($label, $value, $unit) {
      echo "<p><b>$label</b>: $value $unit</p>";
    }
    
    function outputFlagStatistic($label, $value, $unit) {
      $value = $value == 0 ? 'false' : 'true';
      echo "<p><b>$label</b>: $value $unit</p>";
    }
    
    function outputStatisticWithProgress($label, $value, $unit, $maxVal) {
      echo "<p><b>$label</b>: $value $unit <progress value=\"$value\" max=\"$maxVal\"></progress></p>";
    }
    
    ?>
  </body>
</html>
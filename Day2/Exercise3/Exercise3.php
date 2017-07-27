<?php
  
  define('DS', DIRECTORY_SEPARATOR);
  define('ABSPATH', dirname(__FILE__) . DS);

  $filename = $_POST['filename'];
?>
<html>
  <head>
  </head>
  <body>
    <table border="1" style="border: 2px;">
      
    <?php
      $fileHandle = fopen($filename, 'r');
      flock($fileHandle, LOCK_SH);
      if (!$fileHandle) {
        echo "<p>Big fat failure</p>";
        exit();
      }
      
      // headers row
      echo '<tr style="background: #cccccc;">';
      $headings = fgetcsv($fileHandle, 0, ",");
      $numberOfColumns = count($headings);
      for ($i = 0; $i < $numberOfColumns; $i++) {
        echo "<td style=\"width: 20%; text-align: center;\">$headings[$i]</td>";
      }
      echo '</tr>';
      
      while (!feof($fileHandle)) {
        $row = fgetcsv($fileHandle, 0, ",");
        echo '<tr>';
        for ($i = 0; $i < $numberOfColumns; $i++) {
          echo "<td style=\"width: 20%; text-align: center;\">$row[$i]</td>";
        }
        echo '</tr>';
      }
    
      flock($fileHandle, LOCK_UN);
      fclose($fileHandle);
    ?>
      
    </table>
  </body>
</html>
<?php
  define("DS", DIRECTORY_SEPARATOR);
  define('ABSPATH', dirname(__FILE__) . DS);
  $ordersFilename = ABSPATH . "orders.txt";
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Richard's Auto Parts – Customers Orders</title>
  </head>
  <body>
    <h1>Richard's Auto Parts</h1>
    <h2>Customers Orders</h2>
    <table border="1" style="border: 2px;">
    <tr style="background: #cccccc;">
      <td style="width: 20%; text-align: center;">Date/Time</td>
      <td style="width: 10%; text-align: center;">Tires</td>
      <td style="width: 10%; text-align: center;">Oil</td>
      <td style="width: 10%; text-align: center;">Spark
      Plugs</td>
      <td style="width: 10%; text-align: center;">Total
      Cost</td>
      <td style="width: 40%; text-align: center;">Address</td>
    </tr>
      
    <?php
      $readOrdersHandle = fopen($ordersFilename, 'r');
      flock($readOrdersHandle, LOCK_SH);
      if (!$readOrdersHandle) {
        echo "<p>No orders pending - Please try again later</p>";
        exit();
      }
      
      while (!feof($readOrdersHandle)) {
        $order = fgetcsv($readOrdersHandle, 0, "\t");
        
        echo '<tr>';
        echo "<td style=\"text-align: center;\">$order[0]</td>";
        echo "<td style=\"text-align: center;\">$order[1]</td>";
        echo "<td style=\"text-align: center;\">$order[2]</td>";
        echo "<td style=\"text-align: center;\">$order[3]</td>";
        echo "<td style=\"text-align: center;\">£$order[4]</td>";
        echo "<td style=\"text-align: center;\">$order[5]</td>";
        echo "</tr>";

      }
      
      flock($readOrdersHandle, LOCK_UN);
      fclose($readOrdersHandle);
    ?>
    </table>
  </body>
</html>
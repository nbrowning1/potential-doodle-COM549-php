<?php

  define('DS', DIRECTORY_SEPARATOR);
  define('ABSPATH', dirname(__FILE__) . DS);

  define('TIREPRICE', 100);
  define('OILPRICE', 10);
  define('SPARKPRICE', 4);
  define('TAXRATE', 0.20);

  $tireQty = $_POST['tireqty'];
  $oilQty = $_POST['oilqty'];
  $sparkQty = $_POST['sparkqty'];
  $address = $_POST['address'];

  require '../../lib/vendor/autoload.php';
  use Carbon\Carbon;
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Neil's Auto Parts - Order Results</title>
  </head>
  <body>
    <h1>Neil's Auto Parts</h1>
    <h2>Order Results</h2> 
    <?php
      
      $nowInLondonTz = Carbon::now(new DateTimeZone('Europe/London'));
      
      if(!isset($tireQty) || !isset($oilQty) || !isset($sparkQty) || !is_numeric($tireQty) || !is_numeric($oilQty) || !is_numeric($sparkQty) ) {
        // Error processing the order
        echo '<p style="color:red">Your order is incomplete</p>';
        echo "<p>Please enter a valid numeric amount for the following:<p/><ul>";

        if(!isset($tireQty) || !is_numeric($tireQty)) {
          echo " <li>Tires</li>"; 
        }
        
        if(!isset($oilQty) || !is_numeric($oilQty)) {
          echo " <li>Oil</li>"; 
        }
        
        if(!isset($sparkQty) || !is_numeric($sparkQty)) {
          echo " <li>Spark Plugs</li>";
        }
      } else {
        // Order processed correctly

        $totalQty = $tireQty + $oilQty + $sparkQty;

        echo "<p>Order processed on " . $nowInLondonTz->format('l jS \\of F Y h:i:s A') . "</p>";
        echo "<p>You ordered $totalQty item(s)</p>";

        echo '<p>Your order is as follows: </p>';
        echo "<ul>";

        $tireCost = $tireQty * TIREPRICE;
        $oilCost = $oilQty * OILPRICE;
        $sparkCost = $sparkQty * SPARKPRICE;

        if($tireQty == 1) {
          echo " <li>" . htmlspecialchars($tireQty)  . " tire costing £$tireCost</li>";
        } else {
          echo " <li>" . htmlspecialchars($tireQty)  . " tires costing £$tireCost</li>";
        }

        if($oilQty == 1) {
          echo " <li>" . htmlspecialchars($oilQty)  . " bottle of oil costing £$oilCost</li>";
        } else {
          echo " <li>" . htmlspecialchars($oilQty)  . " bottles of oil costing £$oilCost</li>";
        }

        if($sparkQty == 1) {
          echo " <li>" . htmlspecialchars($sparkQty)  . " spark plug costing £$sparkCost</li>";
        } else {
          echo " <li>" . htmlspecialchars($sparkQty)  . " spark plugs costing £$sparkCost</li>";
        }
        echo "</ul>";

        $subTotal = $tireCost + $oilCost + $sparkCost;
        $total = $subTotal * (1 + TAXRATE);

        echo '<p style="font-weight:bold">Subtotal: £' . number_format($subTotal,2) . ' <br />';
        echo 'Tax ('. number_format(TAXRATE*100,0) .'%): £' . number_format($total - $subTotal,2) . ' <br />';
        echo 'Total: £' . number_format($total,2) . ' <p/>';

        echo "Shipping Address: $address";
        
        $fileHandle = fopen(ABSPATH . 'orders.txt', 'a');
        $orderText = $nowInLondonTz->format('d/m/Y h:i:s') . "\t $tireQty tires \t $oilQty oil \t $sparkQty spark plugs \t £$total \t $address" . PHP_EOL;
        
        flock($fileHandle, LOCK_EX);
        fwrite($fileHandle, $orderText);
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);
      }
	 ?>  
  </body>
</html>

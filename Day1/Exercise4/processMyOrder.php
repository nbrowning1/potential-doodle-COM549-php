<?php
  require '../../lib/vendor/autoload.php';
  use Carbon\Carbon;

  define('TIREPRICE', 100);
  define('OILPRICE', 10);
  define('SPARKPRICE', 4);
  define('TAXRATE', 0.20);

  $tireQty = $_POST['tireqty'];
  $oilQty = $_POST['oilqty'];
  $sparkQty = $_POST['sparkqty'];

  function validateFormItemAndAddToList($item, $name) {
    if (!isset($item) || !is_numeric($item)) {
      echo "<li>$name</li>";
    }
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Richard's Auto Parts - Order Results</title>
  </head>
  <body>
    <h1>Richard's Auto Parts</h1>
    <h2>Order Results</h2> 
    <?php
      
      if (!isset($tireQty) || !isset($oilQty) || !isset($sparkQty) || !is_numeric($tireQty) || !is_numeric($oilQty) || !is_numeric($sparkQty)) {
        echo '<p style="color:red">Your order is incomplete</p>';
        echo "<p>Please enter a valid numeric amount for the following:</p><ul>";
        validateFormItemAndAddToList($tireQty, "Tires");
        validateFormItemAndAddToList($oilQty, "Oil");
        validateFormItemAndAddToList($sparkQty, "Spark Plugs");
        echo '</ul>';
      } else {
    
        $totalQuantity = $tireQty + $oilQty + $sparkQty;
        
        $nowInLondonTz = Carbon::now(new DateTimeZone('Europe/London'));
        echo '<p>Order processed on ' . $nowInLondonTz->format('l jS \\of F Y h:i:s A') . '</p>';
        
        echo "Total quantity of order: $totalQuantity";

        $tireCost = $tireQty * TIREPRICE;
        $oilCost = $oilQty * OILPRICE;
        $sparkCost = $sparkQty * SPARKPRICE;
        
        echo '<p>Your order is as follows: </p> <ul>';
        echo '<li>' . htmlspecialchars($tireQty) . ' tires costing £' . htmlspecialchars($tireCost) . '</li>';
        echo '<li>' . htmlspecialchars($oilQty) . ' bottles of oil costing £' . htmlspecialchars($oilCost) . '</li>';
        echo '<li>' . htmlspecialchars($sparkQty) . ' spark plugs costing £' . htmlspecialchars($sparkCost) . '</li>';
        echo '</ul>';

        $subTotal = $tireCost + $oilCost + $sparkCost;
        $tax = $subTotal * TAXRATE;
        $total = $subTotal + $tax;

        
        $taxPercent = TAXRATE * 100;
        
        echo 'Subtotal: £' . number_format($subTotal, 2) . ' <br />';
        echo 'Tax (' . $taxPercent . '%): £' . number_format($tax, 2) . ' <br />';
        echo 'Total: £' . number_format($total, 2) . ' <br />';
      }
	 ?>  
  </body>
</html>

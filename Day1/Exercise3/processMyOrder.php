<?php
  $tireQty = $_POST['tireqty'];
  $oilQty = $_POST['oilqty'];
  $sparkQty = $_POST['sparkqty'];

  define('TIREPRICE', 100);
  define('OILPRICE', 10);
  define('SPARKPRICE', 4);
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
		echo "<p>Order processed at " . date('H:i, jS F Y') . "</p>";
     
        $tirePrice = number_format(TIREPRICE * $tireQty, 2);
        $oilPrice = number_format(OILPRICE * $oilQty, 2);
        $sparkPrice = number_format(SPARKPRICE * $sparkQty, 2);
        echo "$tireQty tires at £$tirePrice<br>";
        echo "$oilQty bottles of oil at £$oilPrice<br>";
        echo "$sparkQty spark plugs at £$sparkPrice<br>";
        
        $totalPrice = number_format(($tirePrice + $oilPrice + $sparkPrice) * 1.2, 2);
        echo "Total cost: £$totalPrice";
        
        # echo htmlspecialchars($oilQty) . ' bottles of oil<br>';
        # echo htmlspecialchars($sparkQty) . ' spark plugs<br>';
		?>
   </body>
</html>
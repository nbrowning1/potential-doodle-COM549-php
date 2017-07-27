<!DOCTYPE html> 
<html>
   <head>
      <title>Neil's Auto Parts - Order Results</title>
   </head>
   <body>
      <h1>Neil's Auto Parts</h1>
      <h2>Order Results</h2>
	  <?php
		require '../../lib/vendor/autoload.php';
		use Carbon\Carbon;
		
		$nowInLondonTz = Carbon::now(new DateTimeZone('Europe/London'));
		echo "<p>Order processed at " . $nowInLondonTz . "</p>";
		echo Carbon::now()->subDays(5)->diffForHumans();
		?>
   </body>
</html>
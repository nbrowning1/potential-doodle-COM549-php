<?php 
  class Conversion { 
    public $from; 
    public $to; 
    public $rate; 

    public function __construct($from, $to, $rate) {
      $this->from = $from;
      $this->to = $to;
      $this->rate = $rate;
    }
    
    public function getInverseRate() {
      return (1 / $this->rate);
    }
  } 

  // used class of conversion objects to be more efficient than 2D array -> one conversion allows the inverse to be applied so covers 2 conversions
  $conversions = array
  (
    new Conversion("USD", "EUR", 0.87887),
    new Conversion("USD", "GBP", 0.77358),
    new Conversion("USD", "INR", 64.4634),
    new Conversion("USD", "AUD", 1.30526),
    new Conversion("USD", "CAD", 1.30248),
    new Conversion("USD", "ZAR", 12.9059),
    new Conversion("USD", "NZD", 1.36784),
    new Conversion("USD", "JPY", 112.317),
    new Conversion("USD", "CNY", 6.78031),
    
    new Conversion("EUR", "GBP", 0.88014),
    new Conversion("EUR", "INR", 73.5179),
    new Conversion("EUR", "AUD", 1.48843),
    new Conversion("EUR", "CAD", 1.48594),
    new Conversion("EUR", "ZAR", 14.7389),
    new Conversion("EUR", "NZD", 1.56084),
    new Conversion("EUR", "JPY", 128.088),
    new Conversion("EUR", "CNY", 7.73127),
    
    new Conversion("GBP", "INR", 83.5643),
    new Conversion("GBP", "AUD", 1.69200),
    new Conversion("GBP", "CAD", 1.68909),
    new Conversion("GBP", "ZAR", 16.7607),
    new Conversion("GBP", "NZD", 1.77352),
    new Conversion("GBP", "JPY", 145.602),
    new Conversion("GBP", "CNY", 8.78676),
    
    new Conversion("INR", "AUD", 0.02024),
    new Conversion("INR", "CAD", 0.02021),
    new Conversion("INR", "ZAR", 0.20054),
    new Conversion("INR", "NZD", 0.02122),
    new Conversion("INR", "JPY", 1.74196),
    new Conversion("INR", "CNY", 0.10513),
    
    new Conversion("AUD", "CAD", 0.99822),
    new Conversion("AUD", "ZAR", 9.90549),
    new Conversion("AUD", "NZD", 1.04805),
    new Conversion("AUD", "JPY", 86.0428),
    new Conversion("AUD", "CNY", 5.19370),
    
    new Conversion("CAD", "ZAR", 9.92051),
    new Conversion("CAD", "NZD", 1.05002),
    new Conversion("CAD", "JPY", 86.2016),
    new Conversion("CAD", "CNY", 5.20390),
    
    new Conversion("ZAR", "NZD", 0.10590),
    new Conversion("ZAR", "JPY", 8.69240),
    new Conversion("ZAR", "CNY", 0.52457),
    
    new Conversion("NZD", "JPY", 82.0840),
    new Conversion("NZD", "CNY", 4.95607),
    
    new Conversion("JPY", "CNY", 0.06037),
  );

  $amount = $_POST['amount'];
  $fromCtry = $_POST['from'];
  $toCtry = $_POST['to'];
?> 

<!DOCTYPE html>
<html>
  <head>
    <title>Currency Convertor</title>
  </head>
  <body>
    <h2>Conversion:</h2>
    <?php
      $convertedAmount;
      if ($fromCtry == $toCtry) {
        $convertedAmount = $amount;
      } else {
        foreach ($conversions as $conversion) {
          if ($conversion->from == $fromCtry && $conversion->to == $toCtry) {
            $convertedAmount = number_format($amount * $conversion->rate, 2);
            break;
          }
          // if swapped, just inverse rate
          if ($conversion->to == $fromCtry && $conversion->from == $toCtry) {
            $convertedAmount = number_format($amount * $conversion->getInverseRate(), 2);
            break;
          }
        }
      }
      if (isset($convertedAmount)) {
        echo "<p>$amount $fromCtry</p>";
        echo "<p>= $convertedAmount $toCtry</p>";
      } else {
        echo "<i>Conversion from $fromCtry to $toCtry was not recognised</i>";
      }
    ?>
  </body>
</html>

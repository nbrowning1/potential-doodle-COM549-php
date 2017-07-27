<?php
  // check for uVariables == empty?

  // uVariables from practical3.php
  $fullName = "$uFirstName $uSurname";
  $userType;
  if ($uUserType == 0) {
    $userType = 'Super Admin';
  } else if ($uUserType == 1) {
    $userType = 'Regular';
  } else {
    $userType = 'Unknown';
  }
?>

<html>
  <head>
  </head>
  <body>
    <a href="practical3.html">Logout</a>
    <br>
    <p>Welcome <?php echo $fullName; ?>!</p>
    <p><b>Username:</b> <?php echo $uUsername ?></p>
    <p><b>Registration Date:</b> <?php echo $uDateReg ?></p>
    <p><b>User Type:</b> <?php echo $userType ?></p>
  </body>
</html>
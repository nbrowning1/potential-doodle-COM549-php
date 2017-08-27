<?php
require_once('../../db/connection.php');
require_once('../../../include.php');
require_once('../../utils.php');
?>

<html>
  <head>
    <link rel="stylesheet" href="../../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../css/all.css">
    <script src="../../../js/jquery-3.2.1.min.js"></script>
    <script src="../../../js/utils.js"></script>
    <script src="recover.js"></script>
  </head>
  <body>
    <h2>Recover Account</h2>
    <br>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $db = connectToDb();
      $username = getPostValue('username');
      $answer = getPostValue('answer');
      $password = getPostValue('password');
      
      $renderRecoveryForm = !empty($username) && empty($answer);
      $renderResetForm = !(empty($username) || empty($answer)) && empty($password);
      
      if ($renderRecoveryForm) {
        $username = getPostValue('username');
        $recoveryQuestion = getUserByUsername($db, $username)->recoveryQ;
        showRecoveryForm($username, $recoveryQuestion);
      } else if ($renderResetForm) {
        showResetForm($username, $answer);
      } else {
        showAccountForm();
      }
    } else {
      showAccountForm();
    }
    ?>
    
    <br>
    <a href="../login.html">Login</a>
  </body>
</html>

<?php function showAccountForm() { ?>
  <form id="account-form" method="post">
    <p style="width: 125px; display: inline-block;">Username</p>
    <input id="username" name="username" type="text" placeholder="Username">
    <span id="username-error" class="error"></span>
    <br>
    <input type="submit" value="Recover" style="margin-left: 150px;">
  </form>
<?php } ?>

<?php function showRecoveryForm($username, $question) { ?>
  <form id="recovery-form" method="post">
    <p style="width: 125px; display: inline-block;"><?php echo $question ?></p>
    <input id="answer" name="answer" type="text" placeholder="Answer">
    <span id="answer-error" class="error"></span>
    <br>
    <!-- hidden username field for passing on data from previous form -->
    <input id="username" name="username" type="text" value="<?php echo $username ?>" style="display: none">
    <input type="submit" value="Recover" style="margin-left: 150px;">
  </form>
<?php } ?>

<?php function showResetForm($username, $answer) { ?>
  <form id="reset-password-form" method="post">
    <p style="width: 125px; display: inline-block;">New Password</p>
    <input id="password" name="password" type="password">
    <span id="password-error" class="error"></span>
    <br>
    <p style="width: 125px; display: inline-block;">Confirm Password</p>
    <input id="confirmPassword" name="confirmPassword" type="password">
    <span id="confirm-password-error" class="error"></span>
    <br>
    <!-- hidden username field for passing on data from previous form -->
    <input id="username" name="username" type="text" value="<?php echo $username ?>" style="display: none">
    <!-- hidden answer field for passing on data from previous form -->
    <input id="answer" name="answer" type="text" value="<?php echo $answer ?>" style="display: none">
    <input type="submit" value="Reset" style="margin-left: 150px;">
  </form>
<?php } ?>
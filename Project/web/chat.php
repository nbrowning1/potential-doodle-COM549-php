<?php

include_once('add_new_conversation.php');

session_start();
// if not logged in, redirect to login page
if (!isset($_SESSION['user'])) {
  header("Location: login/login.html");
  session_destroy();
  exit;
}

// define variables and initialize with empty values
$searchErr = "";
$searchVal = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["addConversationIndicator"])) {
    if (empty($_POST["usernameToAdd"])) {
        $searchErr = "Enter a value";
    } else {
      $searchVal = $_POST["usernameToAdd"];
      $conversationAddStatus = addNewConversation($searchVal);
      if (!$conversationAddStatus->success) {
        $searchErr = $conversationAddStatus->msg;
      } else {
        // clear search value on successful search
        $searchVal = "";
      }
    }
  } else if (isset($_POST["createGroupIndicator"])) {
    
  } else {
    throw new Exception('they jus dont recognise they dont recognise like who the who the fuck is u');
  }
}

?>

<html>
  <head>
    <title>Conversations</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/typeahead.css">
    <link rel="stylesheet" href="../css/select2.min.css">
    <link rel="stylesheet" href="../css/all.css">
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/typeahead.js"></script>
    <script src="../js/typeahead-impl.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/select2-impl.js"></script>
    <script src="../js/all.js"></script>
  </head>
  <body>
    
    <!-- search for new conversation -->
    <form id="search-new-conversation" class="nomargin-container" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
      
      <!-- bootstrap search box with icon -->
      <div class="form-group has-feedback nomargin-container">
        <!-- indicates which form is posting - hidden -->
        <input name="addConversationIndicator" type="text" style="display:none" value="notEmpty"/>
        
        <input name="usernameToAdd" type="text" class="form-control typeahead" placeholder="Add somebody..." value="<?php echo htmlspecialchars($searchVal);?>"/>
        <span class="glyphicon glyphicon-search form-control-feedback"></span>
        <p class="error"><?php echo $searchErr;?></p>
        
        <!-- hidden submission to submit with typeahead -->
        <input type="submit" style="display: none">
      </div>
    </form>
    
    <div id="user-section">
      <span><?php echo $_SESSION['user'] ?></span>
      &nbsp;&nbsp;
      <!-- logout -->
      <a href="login/logout.php" class="btn btn-info" role="button">Log out</a>
    </div>
    
    <br>
    <div id="conversations-section">
      <div id="conversations-pane">
      </div>
      <button id="create-group" type="button" class="btn btn-info" data-toggle="modal" data-target="#create-group-modal">Create Group</button>
    </div>
    <div id="chat-section">
      <div id="chat-pane">
      </div>
    </div>
    <input id="send-message" name="message" type="text" class="form-control" placeholder="Send message..."/>
    
    <!-- Modal -->
    <div class="modal fade" id="create-group-modal" tabindex="-1" role="dialog" aria-labelledby="createGroupModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createGroupModalLabel">Create Group</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          
          <form id="create-new-group" class="nomargin-container" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="modal-body">
                <!-- bootstrap search box with icon -->
                <div class="form-group has-feedback nomargin-container">
                  <!-- indicates which form is posting - hidden -->
                  <input name="createGroupIndicator" type="text" style="display:none" value="notEmpty"/>

                  <select id="add-group-users" name="addGroupUsers" class="form-control" multiple="multiple" style="width: 100%;"/>

                  <input name="groupName" type="text" class="form-control" placeholder="Group Name" style="margin-top: 15px;"/>

                  <!-- hidden submission to submit with typeahead -->
                  <input type="submit" style="display: none">
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <input type="submit" class="btn btn-primary" value="Submit">
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
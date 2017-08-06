<?php

session_start();
// if not logged in, redirect to login page
if (!isset($_SESSION['user'])) {
  header("Location: login/login.html");
  session_destroy();
  exit;
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
    <script src="../js/utils.js"></script>
    <script src="../js/manage-conversations.js"></script>
    <script src="../js/chat-options.js"></script>
    <script src="../js/all.js"></script>
  </head>
  <body>
    
    <!-- search for new conversation -->
    <form id="search-new-conversation" class="nomargin-container" method="post">
      
      <!-- bootstrap search box with icon -->
      <div class="form-group has-feedback nomargin-container">
        
        <input id="username-search" name="nameToAdd" type="text" class="form-control typeahead" placeholder="Add somebody..." />
        <span class="glyphicon glyphicon-search form-control-feedback"></span>
        <p id="add-conversation-error" class="error"></p>
        
        <!-- hidden input for group searches -->
        <input id="group-search" name="groupToAdd" type="text" style="display: none">
        
        <!-- hidden submission to submit with typeahead -->
        <input id="search-conversation-submit" type="submit" style="display: none">
      </div>
    </form>
    
    <div id="user-section">
      <span><?php echo $_SESSION['user'] ?></span>
      &nbsp;&nbsp;
      <!-- logout -->
      <a href="login/logout.php" class="btn btn-info" role="button">Log out</a>
    </div>
    
    <br>
    <div id="conversations">
      <!-- used for formatting, to align with chat title on other side - hack but oh well -->
      <p class="title">&nbsp;</p>
      <div id="conversations-section">
        <div id="conversations-pane">
        </div>
      </div>
      <button id="create-group" type="button" class="btn btn-info" data-toggle="modal" data-target="#create-group-modal">Create Group</button>
    </div>
    <div id="chat">
      <p id="chat-title" class="title"><?php echo isset($_SESSION['active']) ? $_SESSION['active'] : '&nbsp;'; ?></p>
      <div id="chat-options" class="dropdown">
        <button class="btn btn-default" data-toggle="dropdown" type="button" aria-expanded="false">Actions <span class="caret"></span></button>
        <ul id="chat-options-dropdown" class="dropdown-menu pull-right" role="menu">
          <!--<li><a href="#">Favourite user</a></li>
          <li class="divider"></li>
          <li><a href="#">Block user</a></li>-->
        </ul>
      </div>
      
      <div id="chat-section">
        <div id="chat-pane">
        </div>
      </div>
      <input id="send-message" name="message" type="text" class="form-control" placeholder="Send message..."/>
    </div>
    
    <!-- Create Group Modal -->
    <div class="modal fade" id="create-group-modal" tabindex="-1" role="dialog" aria-labelledby="createGroupModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createGroupModalLabel">Create Group</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          
          <form id="create-new-group" class="nomargin-container" method="post">
            <div class="modal-body">
                <!-- bootstrap search box with icon -->
                <div class="form-group has-feedback nomargin-container">

                  <select id="add-group-users" name="addGroupUsers[]" class="form-control" multiple="multiple" style="width: 100%;"/>
                  
                  <!-- needed to separate error element from select as select2 processing swallows it for some reason -->
                  <input type="text" style="display:none"/>
                  
                  <span id="add-group-users-error" class="error"></span>

                  <input id="group-name" name="groupName" type="text" class="form-control" placeholder="Group Name" style="margin-top: 15px;"/>
                  <span id="group-name-error" class="error"></span>

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
    
    <!-- Rename Group Modal -->
    <div class="modal fade" id="rename-group-modal" tabindex="-1" role="dialog" aria-labelledby="renameGroupModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="renameGroupModalLabel">Rename Group: <span id="rename-group-title-old-group"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          
          <form id="rename-group" class="nomargin-container" method="post">
            <div class="modal-body">

              <input id="rename-group-name" name="groupName" type="text" class="form-control" placeholder="Group Name" style="margin-top: 15px;"/>
              <span id="rename-group-name-error" class="error"></span>

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
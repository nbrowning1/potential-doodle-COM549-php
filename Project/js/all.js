$(document).ready(function() {
  
  // initial load
  updateConversationsPane();
  
  // constantly poll conversations to update them on new messages etc.
  setInterval(function() {
    updateConversationsPane();
  }, 2000);
  
  function updateConversationsPane() {
    $.ajax({
      type: "POST",
      url: '../web/conversations_refresh.php',
      data: { },
      success: function(html) {
        // refresh conversations
        $("#conversations-pane").html(html);
        
        // make sure chat pane is updated on page load for active chat
        var activeConversations = document.getElementsByClassName('active');
        if (activeConversations[0]) {
          updateChatPane(activeConversations[0]);
        }
      }
    });
  }
  
  // on clicking a conversation, update the active chat window to show the conversation with that person
  $(document).on('click', '.conversation', function(event) {
    
    // update conversations immediately on UI as to which is active
    $(".conversation").each(function(index) {
      $(this).removeClass("active");
    });
    $(this).addClass("active");
    
    updateChatPane(event.target);
  });
  
  $(document).on('click', '.conversation .glyphicon-remove', function(event) {
    // don't want to activate click event for conversation
    event.stopPropagation();
    var conversationToDelete = event.target.parentElement.id;
    
    $.ajax({
      type: "POST",
      url: '../web/conversations_refresh.php',
      data: { hide_id: conversationToDelete },
      success: function(html) {
        // refresh conversations
        $("#conversations-pane").html(html);
      }
    });
  });
  
  function updateChatPane(activeConversationEl) {
    var activeChat = activeConversationEl.id;
    
    $.ajax({
      type: "POST",
      url: '../web/chat_refresh.php',
      data: { active: activeChat },
      success: function(html) {
        // refresh chat pane with html returned by PHP - the applicable messages for this conversation
        $("#chat-pane").html(html);
        goToBottom('chat-section');
      }
    });
  }
  
  $('#send-message').keypress(function (e) {
    // on Enter pressed
    if (e.which == 13) {
      
      var message = $('#send-message').val();
      // clear textbox
      $('#send-message').val('');
      
      $.ajax({
        type: "POST",
        url: '../web/chat_refresh.php',
        data: { use_same_active: true,
                message: message },
        success: function(html) {
          // refresh chat pane with html returned by PHP - the applicable messages for this conversation
          $("#chat-pane").html(html);
          goToBottom('chat-section');
        }
      });
      
      return false;
    }
    
    
  });
  
  function goToBottom(id) {
    var el = document.getElementById(id);
    el.scrollTop = el.scrollHeight - el.clientHeight;
  }
});
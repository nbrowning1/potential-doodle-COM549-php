$(document).ready(function() {
  
  // initial load
  updateConversationsPane();
  
  // constantly poll conversations to update them on new messages etc.
  setInterval(function() {
    updateConversationsPane();
  }, 2000);
  
  // on clicking a conversation, update the active chat window to show the conversation with that person
  $(document).on('click', '.conversation', function(event) {
    
    // update conversations immediately on UI as to which is active
    $(".conversation").each(function(index) {
      $(this).removeClass("active");
    });
    $(this).addClass("active");
    updateChatPane(event.target, true);
  });
  
  $(document).on('click', '.conversation .glyphicon-remove', function(event) {
    // don't want to activate click event for conversation
    event.stopPropagation();
    var conversationToDeleteEl = event.target.parentElement;
    var conversationToDelete = conversationToDeleteEl.id;
    
    var isGroupConversation = isGroupChat(conversationToDeleteEl);
    
    $.ajax({
      type: "POST",
      url: '../web/conversations_refresh.php',
      data: { hide_id: conversationToDelete,
              isGroupConversation: isGroupConversation },
      success: function(html) {
        // refresh conversations
        $("#conversations-pane").html(html);
      }
    });
  });
  
  $('#send-message').keypress(function (e) {
    // on Enter pressed
    if (e.which == 13) {
      
      var message = $('#send-message').val();
      // clear textbox
      $('#send-message').val('');
      
      var activeConversation = getActiveConversationEl();
      var isGroupConversation = activeConversation ? isGroupChat(activeConversation) : false;
      
      $.ajax({
        type: "POST",
        url: '../web/chat_refresh.php',
        data: { use_same_active: true,
                message: message,
                isGroupConversation: isGroupConversation },
        success: function(html) {
          // refresh chat pane with html returned by PHP - the applicable messages for this conversation
          $("#chat-pane").html(html);
          goToBottom('chat-section');
        }
      });
      
      return false;
    }
    
  });
});

function updateConversationsPane() {
  $.ajax({
    type: "POST",
    url: '../web/conversations_refresh.php',
    data: { },
    success: function(html) {
      // refresh conversations
      $("#conversations-pane").html(html);

      // make sure chat pane is updated on page load for active chat
      var activeConversation = getActiveConversationEl();
      if (activeConversation) {
        updateChatPane(activeConversation, false);
      }
    }
  });
}

function updateChatPane(activeConversationEl, scroll) {
  var activeChat = activeConversationEl.id;
  var isGroupConversation = isGroupChat(activeConversationEl);

  $.ajax({
    type: "POST",
    url: '../web/chat_refresh.php',
    data: { active: activeChat,
            isGroupConversation: isGroupConversation },
    success: function(response) {
      // refresh chat pane with html returned by PHP - the applicable messages for this conversation
      $("#chat-pane").html(response.chatContent);
      // and update conversation title
      $('#chat-title').html(response.chatTitle);
      if (scroll) {
        goToBottom('chat-section');
      }
    }
  });
}

function getActiveConversationEl() {
  return document.getElementsByClassName('active')[0];
}

function isGroupChat(conversationEl) {
  var classList = conversationEl.className.split(/\s+/);
  return classList.includes('group-conversation');
}

function goToBottom(id) {
  var el = document.getElementById(id);
  el.scrollTop = el.scrollHeight - el.clientHeight;
}
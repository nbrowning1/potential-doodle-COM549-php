$(document).ready(function() {
  
  // initial load
  updateConversationsPane();
  
  // constantly poll conversations to update them on new messages etc.
  setInterval(function() {
    // only do the heavy lifting and view refreshing if the user has pending changes they haven't seen
    checkViewNeedsRefreshed().then(function(response) {

      if (response.refreshNeeded === true) {
        setHasUpdatesForCurrentUser(false);
        updateConversationsPane();
      
      // explicit boolean check to check valid response returned
      } else if (response.refreshNeeded !== false) {
        console.log('Failure occurred evaluating whether view needs refreshed');
      }
    }).catch(function(err) {
      console.log(err);
    });
  }, 1000);
  
  // constantly checking if unread messages are showing, and refreshing conversations pane if they are, to dismiss any read notifications - if we can find unread messages, we must already be looking at them on the active chat pane
  setInterval(function() {
    if ($(".chat-message.unread-message").length > 0) {
      updateConversationsPane();
      goToBottom('chat-section');
    }
  }, 500);
  
  // on clicking a conversation, update the active chat window to show the conversation with that person
  $(document).on('click', '.conversation', function(event) {
    
    // update conversations immediately on UI as to which is active
    $(".conversation").each(function(index) {
      $(this).removeClass("active");
    });
    $(this).addClass("active");
    updateChatPane(event.target, true);
    
    // after short timeout, update conversations pane to get rid of notifications number etc. - not instant
    setTimeout(updateConversationsPane, 1500);
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
        success: function(response) {
          // refresh chat pane with html returned by PHP - the applicable messages for this conversation
          $("#chat-pane").html(response.chatContent);
          // and update conversation title
          $('#chat-title').html(response.chatTitle);
          goToBottom('chat-section');
        }
      });
      
      return false;
    }
    
  });
});

function checkViewNeedsRefreshed() {
  return new Promise(function(resolve, reject) {
    $.ajax({
      type: "POST",
      url: '../web/refresh_checker.php',
      data: { },
      success: function(response) {
        resolve(response);
      },
      error: function(err) {
        reject(err);
      }
    });
  });
}

function setHasUpdatesForCurrentUser(status) {
  $.ajax({
    type: "POST",
    url: '../web/refresh_updater.php',
    data: { 
      hasUpdates: status
    },
    success: function(response) {
      if (!response.successfulUpdate) {
        console.log('Error occurred setting refresh not needed');
      }
    }
  });
}

function updateConversationsPane(updatedConversationName) {
  $.ajax({
    type: "POST",
    url: '../web/conversations_refresh.php',
    data: { },
    success: function(html) {
      // refresh conversations
      $("#conversations-pane").html(html);
      
      if (updatedConversationName) {
        // if conversation name updated, active will be out of date and we should re-set active conversation to the newly renamed conversation
        var renamedConversation = $(".conversation[id='" + updatedConversationName + "']")[0];
        renamedConversation.click();
        
      } else {
        // make sure chat pane is updated on page load for active chat
        var activeConversation = getActiveConversationEl();
        if (activeConversation) {
          updateChatPane(activeConversation, false);
        }
      }
    }
  });
}

function updateChatPane(activeConversationEl, scroll) {
  var activeChat = activeConversationEl.id;
  var isGroupConversation = isGroupChat(activeConversationEl);
  
  populateOptionsDropdown(isGroupConversation);

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

function isGroupChat(conversationEl) {
  var classList = conversationEl.className.split(/\s+/);
  return classList.includes('group-conversation');
}

function goToBottom(id) {
  var el = document.getElementById(id);
  el.scrollTop = el.scrollHeight - el.clientHeight;
}
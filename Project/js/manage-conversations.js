$(document).ready(function() {
  
  $(document).on('submit', 'form#search-new-conversation', function(e) {
    e.preventDefault();
    
    // clear down error fields from previous attempt (with a space for this particular field to keep things aligned)
    var addConversationErrorEl = $('#add-conversation-error');
    addConversationErrorEl.html('&nbsp;');
    
    var formData = serializeForm('#search-new-conversation');
    
    var usernameField = $('#username-search');
    var groupField = $('#group-search');
    
    $.ajax({
      url: '../conversations/add_active_conversation.php',
      type: 'POST',
      data: {
        nameToAdd: formData['nameToAdd'],
        groupToAdd: formData['groupToAdd']
      },
      success: function(data) {
        if (data.error) {
          if (data.searchError) {
            addConversationErrorEl.text(data.searchError);
          }
        } else {
          // clear field values on success
          usernameField.blur();
          usernameField.val("");
          groupField.val("");
          
          // set active to newly added conversation
          $.ajax({
            url: '../conversations/update_active_conversation.php',
            type: 'POST',
            data: {
              newActive: formData['nameToAdd']
            },
            success: function(data) {
              updateConversationsPane();
            }
          });
        }
      }
    });
    
    // don't want form to actually submit - no refresh
    return false;
  });
  
  $(document).on('submit', 'form#create-new-group', function(e) {
    e.preventDefault();
    
    // clear down error fields from previous attempt
    var groupUsersErrorEl = $('#add-group-users-error');
    var groupNameErrorEl = $('#group-name-error');
    groupUsersErrorEl.text('');
    groupNameErrorEl.text('');
    
    var formData = serializeForm('#create-new-group');
    
    $.ajax({
      url: '../conversations/create_group_conversation.php',
      type: 'POST',
      data: {
        groupName: formData['groupName'],
        members: formData['addGroupUsers[]']
      },
      success: function(data) {
        if (data.error) {
          if (data.membersError) {
            groupUsersErrorEl.text(data.membersError);
          }
          if (data.groupNameError) {
            groupNameErrorEl.text(data.groupNameError);
          }
        } else {
          // dismiss modal
          $('#create-group-modal').modal('toggle');
          
          // and clear field values
          $('#add-group-users').text('');
          $('#group-name').val('');
          
          // set active to newly added conversation and refresh
          $.ajax({
            url: '../conversations/update_active_conversation.php',
            type: 'POST',
            data: {
              newActive: formData['groupName']
            },
            success: function(data) { 
              updateConversationsPane();
            }
          });
        }
      }
    });
    
    // don't want form to actually submit - no refresh
    return false;
  });

});
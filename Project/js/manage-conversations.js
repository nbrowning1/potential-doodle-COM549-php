$(document).ready(function() {
  
  $(document).on('submit', 'form#search-new-conversation', function(e) {
    e.preventDefault();
    
    // clear down error fields from previous attempt
    var addConversationErrorEl = $('#add-conversation-error');
    addConversationErrorEl.text('');
    
    var formData = serializeForm('#search-new-conversation');
    
    var searchField = $('#username-search');
    
    $.ajax({
      url: '../web/conversations/add_active_conversation.php',
      type: 'POST',
      data: {
        nameToAdd: formData['nameToAdd'],
        groupToAdd: formData['groupToAdd']
      },
      success: function(data) {
        if (data.error) {
          if (data.searchError) {
            addConversationErrorEl.text(data.searchError);
            searchField.blur();
          }
        } else {
          // clear search value on success
          searchField.val('');
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
      url: '../web/conversations/create_group_conversation.php',
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
        }
      }
    });
    
    // don't want form to actually submit - no refresh
    return false;
  });

});
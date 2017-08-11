$(document).ready(function() {
  
  // chat options
  $(document).on('click', asId(GROUP_ADD_MEMBER_ID), function(event) {
    var groupName = getActiveId();
    $('#add-member-group-title-current-group').text(groupName);
  });
  
  $(document).on('click', asId(GROUP_RENAME_ID), function(event) {
    var groupName = getActiveId();
    $('#rename-group-name').val(groupName);
    $('#rename-group-title-old-group').text(groupName);
  });
  
  $(document).on('click', asId(GROUP_LEAVE_ID), function(event) {
    var groupName = getActiveId();
    $('#leave-group-title-current-group').text(groupName);
  });
  
  $(document).on('click', asId(FAVOURITE_USER_ID), function(event) {
    console.log('favourite user for ' + getActiveId());
  });
  
  $(document).on('click', asId(BLOCK_USER_ID), function(event) {
    console.log('block user for ' + getActiveId());
  });
  
  function getActiveId() {
    return getActiveConversationEl().id;
  }
  
  // modals
  // ADD MEMBERS TO GROUP
  $(document).on('submit', 'form#add-member-group', function(e) {
    e.preventDefault();
    
    var groupName = $('#add-member-group-title-current-group').text();
    
    // clear down error fields from previous attempt
    var addMemberGroupErrorEl = $('#add-member-existing-group-error');
    addMemberGroupErrorEl.text('');
    
    var formData = serializeForm('#add-member-group');
    
    $.ajax({
      url: '../web/conversations/add_members_group_conversation.php',
      type: 'POST',
      data: {
        groupName: groupName,
        members: formData['addGroupUsers[]']
      },
      success: function(data) {
        if (data.error) {
          if (data.membersError) {
            addMemberGroupErrorEl.text(data.membersError);
          }
        } else {
          // dismiss modal
          $('#add-member-group-modal').modal('toggle');
          
          // and clear field values
          $('#add-group-users').text('');
          updateConversationsPane();
        }
      }
    });
    
    // don't want form to actually submit - no refresh
    return false;
  });
  
  // RENAME GROUP
  $(document).on('submit', 'form#rename-group', function(e) {
    e.preventDefault();
    
    var oldName = $('#rename-group-title-old-group').text();
    
    // clear down error fields from previous attempt
    var renameGroupErrorEl = $('#rename-group-name-error');
    renameGroupErrorEl.text('');
    
    var formData = serializeForm('#rename-group');
    var newName = formData['groupName'];
    
    $.ajax({
      url: '../web/conversations/rename_group_conversation.php',
      type: 'POST',
      data: {
        oldName: oldName,
        newName: newName
      },
      success: function(data) {
        if (data.error) {
          if (data.groupNameError) {
            renameGroupErrorEl.text(data.groupNameError);
          }
        } else {
          // dismiss modal
          $('#rename-group-modal').modal('toggle');
          updateConversationsPane(newName);
        }
      }
    });
    
    // don't want form to actually submit - no refresh
    return false;
  });
  
  // LEAVE GROUP
  $(document).on('click', '#leave-group-confirm-btn', function(e) {
    var groupName = $('#leave-group-title-current-group').text();
    
    $.ajax({
      url: '../web/conversations/leave_group_conversation.php',
      type: 'POST',
      data: {
        groupName: groupName
      },
      success: function(data) {
        if (data.error) {
          // TODO : do something
        } else {
          // dismiss modal
          $('#leave-group-modal').modal('toggle');
          updateConversationsPane();
        }
      }
    });
  });
});

const GROUP_ADD_MEMBER_ID = 'options-add-member';
const GROUP_RENAME_ID = 'options-rename-group';
const GROUP_LEAVE_ID = 'options-leave-group';
const FAVOURITE_USER_ID = 'options-favourite-user';
const BLOCK_USER_ID = 'options-block-user';
  
function asId(idName) {
  return '#' + idName;
}

function populateOptionsDropdown(isGroupConversation) {
  var chatOptionsDropdown = document.getElementById('chat-options-dropdown');
  $(chatOptionsDropdown).empty();
  if (isGroupConversation) {
    chatOptionsDropdown.appendChild(createDropdownItem(GROUP_ADD_MEMBER_ID, 'Add member', 'add-member-group-modal'));
    chatOptionsDropdown.appendChild(createDropdownItem(GROUP_RENAME_ID, 'Rename group', 'rename-group-modal'));
    chatOptionsDropdown.appendChild(createDropdownDivider());
    chatOptionsDropdown.appendChild(createDropdownItem(GROUP_LEAVE_ID, 'Leave group', 'leave-group-modal'));
  } else {
    chatOptionsDropdown.appendChild(createDropdownItem(FAVOURITE_USER_ID, 'Favourite user'));
    chatOptionsDropdown.appendChild(createDropdownDivider());
    chatOptionsDropdown.appendChild(createDropdownItem(BLOCK_USER_ID, 'Block user'));
  }
  
  function createDropdownItem(id, text, modalId) {
    var item = document.createElement('li');
    var link = document.createElement('a');
    link.href = '#';
    link.id = id;
    link.textContent = text;
    item.appendChild(link);
    if (modalId) {
      item.setAttribute('data-toggle', 'modal');
      item.setAttribute('data-target', '#' + modalId);
    }
    return item;
  }

  function createDropdownDivider() {
    var divider = document.createElement('li');
    divider.className = 'divider';
    return divider;
  }
}
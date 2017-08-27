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
    var userToFavourite = getActiveId();
    $.ajax({
      url: '../web/conversations/manage_favourite_user.php',
      type: 'POST',
      data: {
        username: userToFavourite,
        favouriteStatus: 'favourite'
      },
      success: function(data) {
        if (data.error) {
          // TODO : do something
        } else {
          updateConversationsPane();
        }
      }
    });
  });
  
  $(document).on('click', asId(UNFAVOURITE_USER_ID), function(event) {
    var userToUnfavourite = getActiveId();
    $.ajax({
      url: '../web/conversations/manage_favourite_user.php',
      type: 'POST',
      data: {
        username: userToUnfavourite,
        favouriteStatus: 'unfavourite'
      },
      success: function(data) {
        if (data.error) {
          // TODO : do something
        } else {
          updateConversationsPane();
        }
      }
    });
  });
  
  $(document).on('click', asId(BLOCK_USER_ID), function(event) {
    var usernameToBlock = getActiveId();
    $('#block-user-title-user-name').text(usernameToBlock);
  });
  
  $(document).on('click', asId(UNBLOCK_USER_ID), function(event) {
    var usernameToUnblock = getActiveId();
    $.ajax({
      url: '../web/conversations/manage_block_user.php',
      type: 'POST',
      data: {
        username: usernameToUnblock,
        blockStatus: 'unblock'
      },
      success: function(data) {
        if (data.error) {
          // TODO : do something
        } else {
          updateConversationsPane();
        }
      }
    });
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
  
  // BLOCK USER
  $(document).on('click', '#block-user-confirm-btn', function(e) {
    var username = $('#block-user-title-user-name').text();
    
    $.ajax({
      url: '../web/conversations/manage_block_user.php',
      type: 'POST',
      data: {
        username: username,
        blockStatus: 'block'
      },
      success: function(data) {
        if (data.error) {
          // TODO : do something
        } else {
          // dismiss modal
          $('#block-user-modal').modal('toggle');
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
const UNFAVOURITE_USER_ID = 'options-unfavourite-user';
const BLOCK_USER_ID = 'options-block-user';
const UNBLOCK_USER_ID = 'options-unblock-user';
  
function asId(idName) {
  return '#' + idName;
}

function populateOptionsDropdown(isGroupConversation, isBlockedUser, isFavouritedUser) {
  var chatOptionsDropdown = document.getElementById('chat-options-dropdown');
  $(chatOptionsDropdown).empty();
  if (isGroupConversation) {
    chatOptionsDropdown.appendChild(createDropdownItem(GROUP_ADD_MEMBER_ID, 'Add member', 'add-member-group-modal'));
    chatOptionsDropdown.appendChild(createDropdownItem(GROUP_RENAME_ID, 'Rename group', 'rename-group-modal'));
    chatOptionsDropdown.appendChild(createDropdownDivider());
    chatOptionsDropdown.appendChild(createDropdownItem(GROUP_LEAVE_ID, 'Leave group', 'leave-group-modal'));
  } else {
   
    // allow unfavourite if user is already favourited, and don't allow favouriting a blocked user (until unblocked)
    if (isFavouritedUser) {
      chatOptionsDropdown.appendChild(createDropdownItem(UNFAVOURITE_USER_ID, 'Unfavourite user'));
    } else if (!isBlockedUser) {
      chatOptionsDropdown.appendChild(createDropdownItem(FAVOURITE_USER_ID, 'Favourite user'));
    }
    
    // only need divider if user isn't blocked or favourited - otherwise there will only be one option (to unblock or unfavourite)
    if (!(isFavouritedUser || isBlockedUser)) {
      chatOptionsDropdown.appendChild(createDropdownDivider());
    }
   
    // allow unblock if user is already blocked, and don't allow blocking a favourited user (until unfavourited)
    if (isBlockedUser) {
      chatOptionsDropdown.appendChild(createDropdownItem(UNBLOCK_USER_ID, 'Unblock user'));
    } else if (!isFavouritedUser) {
      chatOptionsDropdown.appendChild(createDropdownItem(BLOCK_USER_ID, 'Block user', 'block-user-modal'));
    }
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
<?php

function insertGroupUsersForGroup($db, $groupId, $groupUsers) {
  
  foreach ($groupUsers as $groupUser) {
    $query = 'INSERT INTO groups_users(group_id, user_id, group_visibility) VALUES (?, ?, 1)';
    $stmt = $db->prepare($query);

    
    $stmt->bind_param('ii', $groupId, $groupUser->id);
    $stmt->execute();
    if ($stmt->error) {
      echo $stmt->error;
      throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
    }

    $stmt->free_result();
    
    // mark each user as having updates so their conversations will refresh with the new group
    setUserHasUpdatesById($db, $groupUser->id, 1);
  } 
}

function getGroupUsersForGroup($db, $group) {
  $stmt = $db->prepare('SELECT * FROM groups_users WHERE group_id = ?');
  
  $stmt->bind_param('i', $group->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($guId, $guGroupId, $guUserId, $guGroupVisibility);
  
  $groupUsers = array();
  while ($stmt->fetch()) {
    array_push($groupUsers, new GroupUser($guId, $group, getUserById($db, $guUserId), $guGroupVisibility));
  }
  
  return $groupUsers;
}

function getGroupIdsForUser($db, $user) {
  $stmt = $db->prepare('SELECT * FROM groups_users WHERE user_id = ? AND group_visibility = 1');
  
  $stmt->bind_param('i', $user->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($guId, $guGroupId, $guUserId, $guGroupVisibility);
  
  $groupIds = array();
  while ($stmt->fetch()) {
    array_push($groupIds, $guGroupId);
  }
  
  return $groupIds;
}

function groupVisibleForUser($db, $groupConversationId, $user) {
  $stmt = $db->prepare('SELECT * FROM groups_users WHERE group_id = ? AND user_id = ?');
  
  $stmt->bind_param('ii', $groupConversationId, $user->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($guId, $guGroupId, $guUserId, $guGroupVisibility);
  
  $stmt->fetch();
  
  return $guGroupVisibility;
}

// TODO : 2 queries into 1 - UPDATE .. SET .. = NOT(..) WHERE group_id = .. AND user_id = .. - should maybe just have a setTrue and setFalse method though - toggle doesnt really make sense
function updateGroupUserGroupVisibility($db, $groupConversationId, $user) {
  
  $stmt = $db->prepare('SELECT * FROM groups_users WHERE group_id = ? AND user_id = ?');
  
  $stmt->bind_param('ii', $groupConversationId, $user->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($guId, $guGroupId, $guUserId, $guGroupVisibility);
  
  $stmt->fetch();
  
  // invert visibility
  $newVisible = $guGroupVisibility ? 0 : 1;
  
  $sql = "UPDATE groups_users SET group_visibility = $newVisible WHERE id = $guId";
  
  $db->query($sql);
}

function setGroupUserGroupVisibleToTrue($db, $groupConversationId, $userId) {
  
  $stmt = $db->prepare("UPDATE groups_users SET group_visibility = 1 WHERE group_id = ? AND user_id = ?");
  
  $stmt->bind_param('ii', $groupConversationId, $userId);
  $stmt->execute();
}


?>
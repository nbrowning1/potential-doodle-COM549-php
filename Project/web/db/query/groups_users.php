<?php

function insertGroupUsersForGroup($db, $groupId, $groupUsers) {
  
  foreach ($groupUsers as $groupUser) {
    $query = 'INSERT INTO groups_users(group_id, user_id, group_visibility) VALUES (?, ?, 1)';
    $stmt = $db->prepare($query);

    
    $stmt->bind_param('ii', $groupId, $groupUser->id);
    $stmt->execute();
    if ($stmt->error) {
      throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
    }

    $stmt->free_result();
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
    array_push($groupUsers, new GroupUser($guId, $group, getUserById($db, $guUserId)), $guGroupVisibility);
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

function updateGroupUserGroupVisibility($db, $groupConversationId, $user) {
  $stmt = $db->prepare('SELECT * FROM groups_users WHERE group_id = ? AND user_id = ?');
  
  $stmt->bind_param('ii', $groupConversationId, $user->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($guId, $guGroupId, $guUserId, $guGroupVisibility);
  
  // invert visibility
  $newVisible = $guGroupVisibility ? 0 : 1;
  
  $sql = "UPDATE groups_users SET group_visibility = $newVisible WHERE id = $guId";
  
  $db->query($sql);
}

?>
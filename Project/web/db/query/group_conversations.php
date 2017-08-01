<?php

function insertGroupConversationToDb($db, $groupName, $ownerUser, $groupUsers) {
  $query = 'INSERT INTO group_conversations(owner_id, name, visible) VALUES (?, ?, 1)';
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('is', $ownerUser->id, $groupName);
  $stmt->execute();
  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
  
  $groupId = mysqli_insert_id($db);
  
  insertGroupUsersForGroup($db, $groupId, $groupUsers);
}

function getAllGroups($db) {
  $stmt = $db->prepare('SELECT * FROM group_conversations WHERE visible = true ORDER BY name');
  
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($gcId, $gcOwnerId, $gcName, $gcVisible);
  
  $groups = array();
  while ($stmt->fetch()) {
    $group = createGroupObj($db, $gcId, $gcOwnerId, $gcName, $gcVisible);
    array_push($groups, $group);
  }
  
  return $groups;
}

function getGroupById($db, $groupConversationId) {
  $stmt = $db->prepare('SELECT * FROM group_conversations WHERE id = ?');
  
  $stmt->bind_param('i', $groupConversationId);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($gcId, $gcOwnerId, $gcName, $gcVisible);
  
  $stmt->fetch();
  
  return createGroupObj($db, $gcId, $gcOwnerId, $gcName, $gcVisible);
}

function getGroupByName($db, $groupName) {
  $stmt = $db->prepare('SELECT * FROM group_conversations WHERE name = ?');
  
  $stmt->bind_param('s', $groupName);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($gcId, $gcOwnerId, $gcName, $gcVisible);
  
  $stmt->fetch();
  
  return createGroupObj($db, $gcId, $gcOwnerId, $gcName, $gcVisible);
}

function getGroupsForUser($db, $user) {
  $allGroups = getAllGroups($db);
  
  $groupIdsUserBelongsTo = getGroupIdsForUser($db, $user);
  
  $groupsForUser = array();
  foreach ($allGroups as $group) {
    $userOwnsGroup = $group->owner->id == $user->id;
    $userPartOfGroup = in_array($group->id, $groupIdsUserBelongsTo);
    
    if ($userOwnsGroup || $userPartOfGroup) {
      array_push($groupsForUser, $group);
    }
  }
  
  return $groupsForUser;
}

function updateGroupConversationVisibility($db, $groupConversationId) {
  $group = getGroupById($db, $groupConversationId);
  
  // invert visibility
  $newVisible = $group->visible ? 0 : 1;
  
  $sql = "UPDATE group_conversations SET visible = $newVisible WHERE id = $groupConversationId";
  
  $db->query($sql);
}

function createGroupObj($db, $id, $ownerId, $name, $visible) {
  $ownerUser = getUserById($db, $ownerId);
  $group = new GroupConversation($id, $ownerUser, $name, $visible);
  $group->members = getGroupUsersForGroup($db, $group);
  
  return $group;
}

?>
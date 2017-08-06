<?php

function insertGroupConversationToDb($db, $groupName, $groupUsers) {

  $query = 'INSERT INTO group_conversations(name) VALUES (?)';
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('s', $groupName);
  $stmt->execute();
  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
  
  $groupId = mysqli_insert_id($db);
  
  insertGroupUsersForGroup($db, $groupId, $groupUsers);
}

function getAllGroups($db) {
  $stmt = $db->prepare('SELECT * FROM group_conversations ORDER BY name');
  
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($gcId, $gcName);
  
  $groups = array();
  while ($stmt->fetch()) {
    $group = createGroupObj($db, $gcId, $gcName);
    array_push($groups, $group);
  }
  
  return $groups;
}

function getGroupById($db, $groupConversationId) {
  $stmt = $db->prepare('SELECT * FROM group_conversations WHERE id = ?');
  
  $stmt->bind_param('i', $groupConversationId);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($gcId, $gcName);
  
  $stmt->fetch();
  
  return createGroupObj($db, $gcId, $gcName);
}

function getGroupByName($db, $groupName) {
  $stmt = $db->prepare('SELECT * FROM group_conversations WHERE name = ?');
  
  $stmt->bind_param('s', $groupName);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($gcId, $gcName);
  
  $stmt->fetch();
  
  return createGroupObj($db, $gcId, $gcName);
}

function getGroupsForUser($db, $user) {
  $allGroups = getAllGroups($db);
  
  $groupIdsUserBelongsTo = getGroupIdsForUser($db, $user);
  
  $groupsForUser = array();
  foreach ($allGroups as $group) {
    $userPartOfGroup = in_array($group->id, $groupIdsUserBelongsTo);
    
    if ($userPartOfGroup) {
      array_push($groupsForUser, $group);
    }
  }
  
  return $groupsForUser;
}

function updateGroupName($db, $oldName, $newName) {
  $query = 'UPDATE group_conversations SET name = ? WHERE name = ?';
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('ss', $newName, $oldName);
  $stmt->execute();
  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
}

function createGroupObj($db, $id, $name) {
  $group = new GroupConversation($id, $name);
  $group->members = getGroupUsersForGroup($db, $group);
  
  return $group;
}

?>
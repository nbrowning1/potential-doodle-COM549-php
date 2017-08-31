<?php

// check if group name is unique for creation
function groupNameIsAvailable($db, $name) {
  $name = htmlspecialchars($name);
  
  $query = 'SELECT * FROM group_conversations WHERE name = ?';
  $stmt = $db->prepare($query);
  $stmt->bind_param('s', $name);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  if ($stmt->num_rows > 0) {
    return false;
  }

  $stmt->free_result();
  
  return true;
}

function insertGroupConversationToDb($db, $groupName, $groupUsers) {

  $groupName = htmlspecialchars($groupName);
  
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

function getGroupsForUser($db, $user, $onlyVisibleGroups) {
  // if we only want visible groups, append extra query
  $additionalQuery = $onlyVisibleGroups ? 'AND group_visibility = 1' : '';
  $query = "SELECT  *
            FROM    group_conversations
            WHERE 	id IN (SELECT group_id FROM groups_users WHERE user_id = ? $additionalQuery) 
            ORDER BY name";
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('i', $user->id);
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
  $groupName = htmlspecialchars($groupName);
  
  $stmt = $db->prepare('SELECT * FROM group_conversations WHERE name = ?');
  
  $stmt->bind_param('s', $groupName);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($gcId, $gcName);
  
  $stmt->fetch();
  
  return createGroupObj($db, $gcId, $gcName);
}

function updateGroupName($db, $oldName, $newName) {
  $oldName = htmlspecialchars($oldName);
  $newName = htmlspecialchars($newName);
  
  $query = 'UPDATE group_conversations SET name = ? WHERE name = ?';
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('ss', $newName, $oldName);
  $stmt->execute();
  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
}

function removeGroupConversationById($db, $groupConversationId) {
  
  $stmt = $db->prepare("DELETE FROM group_conversations WHERE id = ?");
  
  $stmt->bind_param('i', $groupConversationId);
  $stmt->execute();
}

function createGroupObj($db, $id, $name) {
  $group = new GroupConversation($id, $name);
  $group->members = getGroupUsersForGroup($db, $group);
  
  return $group;
}

?>
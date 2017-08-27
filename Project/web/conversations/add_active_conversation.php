<?php

require_once('../include.php');
require_once('manage_conversations.php');

session_start();

$nameToAdd = getPostValue('nameToAdd');
$groupToAdd = getPostValue('groupToAdd');

if (allEmpty($nameToAdd, $groupToAdd)) {
  returnErrorResponse('searchError', 'Name cannot be empty');
}

addActiveConversation($nameToAdd, $groupToAdd);

function addActiveConversation($nameToAdd, $groupToAdd) {
  // either add regular conversation or group conversation
  $conversationAddStatus = empty($groupToAdd) ?addConversationToActiveConversations($nameToAdd) :
  addGroupConversationToActiveConversations($groupToAdd);
  
  if (!$conversationAddStatus->success) {
    returnErrorResponse('searchError', $conversationAddStatus->msg);
  }
}

?>
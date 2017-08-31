<?php

// allows connection to the DB
require_once('db/connection.php');

// DB models - 1-1 with tables in DB, represents table records in the form of PHP objects
require_once('db/model/chat_message.php');
require_once('db/model/conversation.php');
require_once('db/model/group_conversation.php');
require_once('db/model/group_user.php');
require_once('db/model/user.php');
require_once('db/model/user_blocked_user.php');
require_once('db/model/user_chat_message.php');
require_once('db/model/user_favourite_user.php');

// DB queries - 1-1 with tables in DB, associated queries with each table for CRUD operations etc.
require_once('db/query/users.php');
require_once('db/query/user_blocked_users.php');
require_once('db/query/user_favourite_users.php');
require_once('db/query/conversations.php');
require_once('db/query/group_conversations.php');
require_once('db/query/groups_users.php');
require_once('db/query/chat_messages.php');
require_once('db/query/user_chat_messages.php');

// common code use
require_once('utils.php');

?>
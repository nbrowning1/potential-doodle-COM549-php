-- only get the 1 DB for uni
USE `b00652112`;

--
-- Table structures
--
CREATE TABLE `Users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `username` varchar(20) NOT NULL UNIQUE,
 `password` varchar(50) NOT NULL,
 `recovery_question` varchar(50) NOT NULL,
 `recovery_answer` varchar(30) NOT NULL,
 `reg_date` datetime NOT NULL,
 `user_type` int(1) NOT NULL,
 `has_updates` boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Users_Blocked_Users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `blocked_user_id` int(11) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES Users(id),
  FOREIGN KEY (blocked_user_id) REFERENCES Users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Users_Favourite_Users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `favourite_user_id` int(11) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES Users(id),
  FOREIGN KEY (favourite_user_id) REFERENCES Users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Conversations` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_1_id` int(11) NOT NULL,
 `user_2_id` int(11) NOT NULL,
 `user_1_visibility` boolean NOT NULL DEFAULT 0,
 `user_2_visibility` boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  FOREIGN KEY (user_1_id) REFERENCES Users(id),
  FOREIGN KEY (user_2_id) REFERENCES Users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Group_Conversations` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(30) NOT NULL UNIQUE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Groups_Users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `group_id` int(11) NOT NULL,
 `user_id` int(11) NOT NULL,
 `group_visibility` boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  FOREIGN KEY (group_id) REFERENCES Group_Conversations(id),
  FOREIGN KEY (user_id) REFERENCES Users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Chat_Messages` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `creator_id` int(11) NOT NULL,
 `message` varchar(8000) NOT NULL,
 `conversation_id` int(11),
 `group_conversation_id` int(11),
 `datetime` DATETIME DEFAULT CURRENT_TIMESTAMP,
 `admin_message` boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  FOREIGN KEY (creator_id) REFERENCES Users(id) ON DELETE CASCADE,
  FOREIGN KEY (conversation_id) REFERENCES Conversations(id) ON DELETE CASCADE,
  FOREIGN KEY (group_conversation_id) REFERENCES Group_Conversations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Users_Chat_Messages` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `message_id` int(11) NOT NULL,
 `read_status` boolean NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
  FOREIGN KEY (message_id) REFERENCES Chat_Messages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

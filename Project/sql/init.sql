--
-- Database: `Project`
--
CREATE DATABASE IF NOT EXISTS `Project` DEFAULT CHARACTER SET utf8
COLLATE utf8_general_ci;
USE `Project`;

--
-- Table structures
--
CREATE TABLE `Users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `username` varchar(20) NOT NULL UNIQUE,
 `password` varchar(50) NOT NULL,
 `hint` varchar(30) NOT NULL,
 `answer` varchar(30) NOT NULL,
 `reg_date` datetime NOT NULL,
 `user_type` int(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Blocked_Users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `blocked_user_id` int(11) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES Users(id),
  FOREIGN KEY (blocked_user_id) REFERENCES Users(id)
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
  FOREIGN KEY (creator_id) REFERENCES Users(id),
  FOREIGN KEY (conversation_id) REFERENCES Conversations(id),
  FOREIGN KEY (group_conversation_id) REFERENCES Group_Conversations(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Users_Chat_Messages` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `message_id` int(11) NOT NULL,
 `read_status` boolean NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES Users(id),
  FOREIGN KEY (message_id) REFERENCES Chat_Messages(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

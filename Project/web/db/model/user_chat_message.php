<?php

// may need to require chat_message and user here?

class UserChatMessage {
  public $id;
  public $user;
  public $message;
  public $read;

  public function __construct($id, $user, $message, $read) {
    $this->id = $id;    
    $this->user = $user;
    $this->message = $message;
    $this->read = $read;
  }
}

?>
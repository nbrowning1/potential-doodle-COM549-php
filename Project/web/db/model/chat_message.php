<?php

// may need to require user and conversation here?

class ChatMessage {
  public $id;
  public $creator;
  public $message;
  public $conversation;
  public $group_conversation;
  public $date_time;
  public $is_admin_message;

  public function __construct($id, $creator, $message, $conversation, $group_conversation, $date_time, $is_admin_message) {
    $this->id = $id;
    $this->creator = $creator;
    $this->message = $message;
    $this->conversation = $conversation;
    $this->group_conversation = $group_conversation;
    $this->date_time = $date_time;
    $this->is_admin_message = $is_admin_message;
  }
}

?>
<?php

class GroupConversation {
  public $id;
  public $owner;
  public $name;
  public $members;

  public function __construct($id, $owner, $name) {
    $this->id = $id;
    $this->owner = $owner;
    $this->name = $name;
  }
}

?>
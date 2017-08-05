<?php

class GroupConversation {
  public $id;
  public $name;
  public $members;

  public function __construct($id, $name) {
    $this->id = $id;
    $this->name = $name;
  }
}

?>
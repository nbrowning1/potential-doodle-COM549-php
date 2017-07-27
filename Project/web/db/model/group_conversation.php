<?php

// may need to require user here?

class GroupConversation {
  public $id;
  public $owner;
  public $name;
  public $visible;

  public function __construct($id, $owner, $name, $visible) {
    $this->id = $id;
    $this->owner = $owner;
    $this->name = $name;
    $this->visible = $visible;
  }
}

?>
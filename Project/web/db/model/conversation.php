<?php

// may need to require user here?

class Conversation {
  public $id;
  public $user_1;
  public $user_2;
  public $visible;

  public function __construct($id, $user_1, $user_2, $visible) {
    $this->id = $id;
    $this->user_1 = $user_1;
    $this->user_2 = $user_2;
    $this->visible = $visible;
  }
}

?>
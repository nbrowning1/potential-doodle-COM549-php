<?php

// may need to require user here?

class BlockedUser {
  public $id;
  public $user;
  public $blocked_user;

  public function __construct($id, $user, $blocked_user) {
    $this->id = $id;
    $this->user = $user;
    $this->blocked_user = $blocked_user;
  }
}

?>
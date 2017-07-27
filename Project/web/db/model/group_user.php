<?php

// may need to require group and user here?

class GroupUser {
  public $id;
  public $group;
  public $user;

  public function __construct($id, $group, $user) {
    $this->id = $id;
    $this->group = $group;
    $this->user = $user;
  }
}

?>
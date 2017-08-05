<?php

class GroupUser {
  public $id;
  public $group;
  public $user;
  public $group_visibility;

  public function __construct($id, $group, $user, $group_visibility) {
    $this->id = $id;
    $this->group = $group;
    $this->user = $user;
    $this->group_visibility = $group_visibility;
  }
}

?>
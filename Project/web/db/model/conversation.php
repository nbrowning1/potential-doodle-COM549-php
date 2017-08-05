<?php

class Conversation {
  public $id;
  public $user_1;
  public $user_2;
  public $user_1_visibility;
  public $user_2_visibility;

  public function __construct($id, $user_1, $user_2, $user_1_visibility, $user_2_visibility) {
    $this->id = $id;
    $this->user_1 = $user_1;
    $this->user_2 = $user_2;
    $this->user_1_visibility = $user_1_visibility;
    $this->user_2_visibility = $user_2_visibility;
  }
}

?>
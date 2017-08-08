<?php

class User {
  public $id;
  public $username;
  public $password;
  public $hint;
  public $answer;
  public $reg_date;
  public $user_type;
  public $has_updates;

  public function __construct($id, $username, $password, $hint, $answer, $reg_date, $user_type, $has_updates) {
    $this->id = $id;
    $this->username = $username;
    $this->password = $password;
    $this->hint = $hint;
    $this->answer = $answer;
    $this->reg_date = $reg_date;
    $this->user_type = $user_type;
    $this->has_updates = $has_updates;
  }
}

?>
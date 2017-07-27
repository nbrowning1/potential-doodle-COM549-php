<?php

class User {
  public $id;
  public $username;
  public $password;
  public $hint;
  public $answer;
  public $reg_date;
  public $user_type;

  public function __construct($id, $username, $password, $hint, $answer, $reg_date, $user_type) {
    $this->id = $id;
    $this->username = $username;
    $this->password = $password;
    $this->hint = $hint;
    $this->answer = $answer;
    $this->reg_date = $reg_date;
    $this->user_type = $user_type;
  }
}

?>
<?php

class UserFavouriteUser {
  public $id;
  public $user;
  public $favourite_user;

  public function __construct($id, $user, $favourite_user) {
    $this->id = $id;
    $this->user = $user;
    $this->favourite_user = $favourite_user;
  }
}

?>
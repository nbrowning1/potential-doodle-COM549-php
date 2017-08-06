<?php

function successResponse() {
  return array("success"=>true);
}

function errorResponse() {
  return array("error"=>true);
}

function redirectResponse($location) {
  return array("redirectTo"=>$location);
}

function returnJson($array) {
  header('Content-type: application/json');
  echo json_encode($array);
  exit;
}

?>
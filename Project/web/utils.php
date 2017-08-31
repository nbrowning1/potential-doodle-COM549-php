<?php

function isValidUsername($name) {
  return isValidName($name, 3, 20);
}

function isValidGroupName($groupName) {
  return isValidName($groupName, 5, 20);
}

/* checks that a name meets validation checks - that its length doesn't fall outside supplied length bounds, and that it matches regex for alphanumeric with spaces */
function isValidName($name, $minLength, $maxLength) {
  return (strlen($name) >= $minLength) && (strlen($name) <= $maxLength) && (preg_match('/^[a-zA-Z0-9\s]+$/', $name));
}

/* checks that a password meets validation checks - that it is longer than 6 characters, has at least 1 lowercase character, 1 uppercase character and 1 number */
function isValidPassword($password) {
  return (strlen($password) >= 6) && (preg_match('#[a-z]+#', $password)) && (preg_match('#[A-Z]+#', $password)) && (preg_match('#[0-9]+#', $password));
}

/* get a session value given its key name. Defaults to empty string if not set */
function getSessionValue($key) {
  return isset($_SESSION[$key]) ? $_SESSION[$key] : '';
}

/* get a POST value given its parameter name. Defaults to empty string if parameter doesn't exist */
function getPostValue($key) {
  return isset($_POST[$key]) ? $_POST[$key] : '';
}

/* get a POST value given its parameter name, converting to PHP boolean - needed because ajax calls pass values to PHP as a string:
    https://stackoverflow.com/questions/7408976/bool-parameter-from-jquery-ajax-received-as-literal-string-false-true-in-php */
function getPostValueBoolean($key) {
  $postValStr = getPostValue($key);
  
  $postVal;
  if ($postValStr === 'true') {
    $postVal = true;
  } else if ($postValStr === 'false') {
    $postVal = false;
  } else {
    // must already be boolean - assign directly
    $postVal = $postValStr;
  }
  
  return $postVal;
}

/* get a POST value given its parameter name, converting to PHP array */
function getPostValueArray($key) {
  $valArray = array();
  if (isset($_POST[$key])) {
    $postVal = $_POST[$key];
    // if an array, direct assignment otherwise just push the one value
    if (is_array($postVal)) {
      $valArray = $postVal;
    } else {
      array_push($valArray, $postVal);
    }
  }
  
  return $valArray;
}

/* takes a variable number of arguments, returns true if any of them are empty. Handy for form validation */
function anyEmpty(...$items) {
  foreach ($items as $item) {
    if (empty($item)) {
      return true;
    }
  }
  return false;
}

/* takes a variable number of arguments, returns true if all of them are empty */
function allEmpty(...$items) {
  foreach ($items as $item) {
    if (!empty($item)) {
      return false;
    }
  }
  return true;
}

/* returns a base success response as array to build upon for a later JSON response to indicate success */
function successResponse() {
  return array("success"=>true);
}

/* returns a base error response as array to build upon for a later JSON response to indicate failure */
function errorResponse() {
  return array("error"=>true);
}

/* returns a base redirect response to indicate a redirect should be performed via JS - e.g. for cases where PHP's header() doesn't cooperate */
function redirectResponse($location) {
  return array("redirectTo"=>$location);
}

/* return a base JSON success response of { "success": true } */
function returnSuccessResponse() {
  returnJson(successResponse());
}

/* return a base JSON error response of { "error": true } */
function returnErrorResponse($key, $value) {
  returnResponse(errorResponse(), $key, $value);
}

function returnResponse($baseResponse, $key, $value) {
  $baseResponse[$key] = $value;
  returnJson($baseResponse);
}

/* returns a JSON response for AJAX requests to deal with */
function returnJson($array) {
  header('Content-type: application/json');
  echo json_encode($array);
  exit;
}

?>
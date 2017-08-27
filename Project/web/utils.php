<?php

function getPostValue($key) {
  return isset($_POST[$key]) ? $_POST[$key] : '';
}

/* needed because ajax calls pass values to PHP as a string:
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

function anyEmpty(...$items) {
  foreach ($items as $item) {
    if (empty($item)) {
      return true;
    }
  }
  return false;
}

function allEmpty(...$items) {
  foreach ($items as $item) {
    if (!empty($item)) {
      return false;
    }
  }
  return true;
}

function successResponse() {
  return array("success"=>true);
}

function errorResponse() {
  return array("error"=>true);
}

function redirectResponse($location) {
  return array("redirectTo"=>$location);
}

function returnErrorResponse($key, $value) {
  returnResponse(errorResponse(), $key, $value);
}

function returnResponse($baseResponse, $key, $value) {
  $baseResponse[$key] = $value;
  returnJson($baseResponse);
}

function returnJson($array) {
  header('Content-type: application/json');
  echo json_encode($array);
  exit;
}

?>
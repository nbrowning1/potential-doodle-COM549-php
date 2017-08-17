<?php

require_once __DIR__ . '/../lib/vendor/autoload.php';
use GuzzleHttp\Client;
// designed to be called via AJAX (via JS) so return JSON format rather than HTML
header("Content-Type: application/json");

if(!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
  header("Location: index.php");
  exit;
}

// return false if necessary media_id not specified
if(!isset($_GET['media_id']) || empty($_GET['media_id'])) {
  echo json_encode([
    'success' => false
  ]);
  return;
}

// media ID provided by the piece of media we 'liked' - a unique identifier
$media_id = $_GET['media_id'];
// API URI to like the photo on behalf of the user
$requestUri = "https://api.instagram.com/v1/media/{$media_id}/likes";

// send POST request using URI to execute the action
$client = new Client();
$response = $client->post($requestUri, [
  'form_params' => [
    'access_token' => $_SESSION['access_token']['access_token']
  ]
]);

// and return success. Hooray!
$results = json_decode($response->getBody(), true);
echo json_encode([
  'success' => true
]);

?>
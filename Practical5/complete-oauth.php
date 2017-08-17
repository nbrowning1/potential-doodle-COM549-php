<?php

/* This is where our redirect url points to - after
    authorisation during index.php we land here - 
    hopefully with a 'code' GET parameter - our
    authorisation code */
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
require_once __DIR__ . '/../lib/vendor/autoload.php';
$settings = include_once 'settings.php';

session_start();

/* in the case that we don't have authorisation code
    (AKA authorisation or something else failed), simply
    redirect to homepage */
if(!isset($_GET['code'])) {
  header("Location: index.php");
  exit;
}

// create a Guzzle HTTP client
$client = new Client();
try {
  /* POST request to Instagram using our newly-granted
      authorisation code provided by Instagram to get
      an Access Token response */
  $response = $client->post('https://api.instagram.com/oauth/access_token', [
    'form_params' => [
      'client_id' => $settings['client_id'],
      'client_secret' => $settings['client_secret'],
      'grant_type' => 'authorization_code',
      'redirect_uri' => $settings['redirect_uri'],
      'code' => $_GET['code']
    ]
  ]);
} catch (ClientException $e) {
  // if we get HTTP 400 error, authorisation failed
  if ($e->getCode() == 400) {
    $errorResponse = json_decode($e->getResponse()->getBody(), true);
    die("Authentication Error: {$errorResponse['error_message']}");
  }

  throw $e;
}

// finally, get Access Token response and store in session
$result = json_decode($response->getBody(), true);
$_SESSION['access_token'] = $result;
// and redirect to feed.php to finally use our access
header("Location: feed.php");
exit;

?>
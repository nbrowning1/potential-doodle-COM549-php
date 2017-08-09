<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
require_once __DIR__ . '/../lib/vendor/autoload.php';
$settings = include_once 'settings.php';

session_start();

if(!isset($_GET['code'])) {
  header("Location: index.php");
  exit;
}
$client = new Client();
try {
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
  echo "exception $e->msg";
  if($e->getCode() == 400) {
    $errorResponse = json_decode($e->getResponse()->getBody(), true);
    die("Authentication Error: {$errorResponse['error_message']}");
  }

  throw $e;
}
$result = json_decode($response->getBody(), true);
$_SESSION['access_token'] = $result;
header("Location: feed.php");
exit;
?>